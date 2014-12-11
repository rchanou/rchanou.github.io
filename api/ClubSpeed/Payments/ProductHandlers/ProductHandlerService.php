<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;

class ProductHandlerService {

    private static $logic;
    private static $_lazy;

    /**
     * Dummy constructor to prevent any initialization of this class
     */
    private function __construct() {}

    public static function initialize(&$logic) {
        self::$logic = $logic;
        self::$_lazy = array();
    }

    private static function load($prop) {
        $prop = '\ClubSpeed\Payments\ProductHandlers\\' . ucfirst($prop) . 'ProductHandler';
        if (!isset(self::$_lazy[$prop])) {
            self::$_lazy[$prop] = new $prop(self::$logic);
        }
        return self::$_lazy[$prop];
    }

    public static function handle($checkDetail, $metadata = array()) {
        $product = self::$logic->products->get($checkDetail->ProductID);
        $product = $product[0];
        switch($product->ProductType) {
            case Enums::PRODUCT_TYPE_REGULAR:
                return self::load('Regular')->handle($checkDetail, $metadata);
            case Enums::PRODUCT_TYPE_POINT:
                return self::load('Point')->handle($checkDetail, $metadata);
            case Enums::PRODUCT_TYPE_RESERVATION:
                return self::load('Reservation')->handle($checkDetail, $metadata);
            case Enums::PRODUCT_TYPE_GIFT_CARD:
                return self::load('GiftCard')->handle($checkDetail, $metadata);
            // other handlers/logic yet to be determined
            // see enums for other available product types
        }
    }
}