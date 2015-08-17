<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Logging\LogService as Log;

class ProductHandlerService {

    private static $logic;
    private static $db;
    private static $_lazy;

    /**
     * Dummy constructor to prevent any initialization of this class
     */
    private function __construct() {}

    public static function initialize(&$logic, &$db) {
        self::$logic = $logic;
        self::$db = $db;
        self::$_lazy = array();
    }

    private static function load($prop) {
        $prop = '\ClubSpeed\Payments\ProductHandlers\\' . ucfirst($prop) . 'ProductHandler';
        if (!isset(self::$_lazy[$prop]))
            self::$_lazy[$prop] = new $prop(self::$logic, self::$db);
        return self::$_lazy[$prop];
    }

    public static function handle($checkTotals = array(), $metadatas = array()) {
        try {
            self::$db->begin();
            $results = array();
            foreach($checkTotals as $checkTotal) {
                $metadata = Arrays::first($metadatas, function($val, $key, $arr) use ($checkTotal) {
                    return isset($val['checkDetailId']) && $val['checkDetailId'] == $checkTotal->CheckDetailID;
                });
                $product = self::$logic->products->get($checkTotal->ProductID);
                $product = $product[0];
                switch($product->ProductType) {
                    case Enums::PRODUCT_TYPE_REGULAR:
                        $result = self::load('Regular')->handle($checkTotal, $metadata);
                        break;
                    case Enums::PRODUCT_TYPE_POINT:
                        $result = self::load('Point')->handle($checkTotal, $metadata);
                        break;
                    case Enums::PRODUCT_TYPE_RESERVATION:
                        $result = self::load('Reservation')->handle($checkTotal, $metadata);
                        break;
                    case Enums::PRODUCT_TYPE_GIFT_CARD:
                        $result = self::load('GiftCard')->handle($checkTotal, $metadata);
                        break;
                    // other handlers/logic yet to be determined
                    // see enums for other available product types
                }
                if (!empty($result)) {
                    $results[] = array(
                        'checkDetailId' => $checkTotal->CheckDetailID,
                        'description' => $result
                    );
                }
            }
            self::$db->commit(); // only commit db changes if all are successful
            return $results;
        }
        catch(\Exception $e) {
            self::$db->rollback();
            throw $e;
        }
    }

    // public static function revert($checkDetail, $metadata = array()) {
    //     $product = self::$logic->products->get($checkDetail->ProductID);
    //     $product = $product[0];
    //     switch($product->ProductType) {
    //         // case Enums::PRODUCT_TYPE_REGULAR:
    //         //     return self::load('Regular')->revert($checkDetail, $metadata);
    //         // case Enums::PRODUCT_TYPE_POINT:
    //         //     return self::load('Point')->revert($checkDetail, $metadata);
    //         case Enums::PRODUCT_TYPE_RESERVATION:
    //             return self::load('Reservation')->revert($checkDetail, $metadata);
    //         // case Enums::PRODUCT_TYPE_GIFT_CARD:
    //         //     return self::load('GiftCard')->revert($checkDetail, $metadata);
    //     }
    // }
}