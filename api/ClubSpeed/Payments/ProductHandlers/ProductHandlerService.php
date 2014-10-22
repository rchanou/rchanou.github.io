<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;

/**
 * The database interface class
 * for ClubSpeed online booking.
 */
class ProductHandlerService {

    private $payments;
    private $logic;
    private $_lazy;

    public function __construct(&$payments, &$logic) {
        $this->payments = $payments;
        $this->logic = $logic;
        $this->_lazy = array();
    }

    private function load($prop) {
        $prop = '\ClubSpeed\Payments\ProductHandlers\\' . ucfirst($prop) . 'ProductHandler'; // hacky -- we can go back to the old way detailed below, if desired
        if (!isset($this->_lazy[$prop])) {
            $this->_lazy[$prop] = new $prop($this->logic);
        }
        return $this->_lazy[$prop];
    }

    public function handle($checkDetail, $metadata = array()) {
        $product = $this->logic->products->get($checkDetail->ProductID);
        $product = $product[0];
        switch($product->ProductType) {
            case Enums::PRODUCT_TYPE_POINT:
                return $this->load('Point')->handle($checkDetail, $metadata);
                // todo: shuffle off to handle points item? consider heatId in the metadata?
            case Enums::PRODUCT_TYPE_RESERVATION:
                pr("found reservation item");
                // $this->load('Reservation')->handle($checkDetail, $metadata);
                break;
            case Enums::PRODUCT_TYPE_GIFT_CARD:
                return $this->load('GiftCard')->handle($checkDetail, $metadata);
            // other handlers/logic yet to be determined
            // see enums for other available product types
        }
    }
}