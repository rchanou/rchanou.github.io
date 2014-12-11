<?php

namespace ClubSpeed\Logic;

use ClubSpeed\Enums\Enums as Enums;

/**
 * The business logic class
 * for ClubSpeed online booking.
 */
class BookingLogic extends BaseLogic {

    /**
     * Constructs a new instance of the BookingLogic class.
     *
     * The BookingLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->onlineBookings;
    }

    public final function create($params = array()) {
        $logic =& $this->logic;
        return $this->_create($params, function($booking) use (&$logic) {
            if (!isset($booking->IsPublic))
                $booking->IsPublic = true; // default to public visibility
            if ($booking->QuantityTotal <= 0)
                $booking->QuantityTotal = 0; // disallow negatives

            if (!isset($booking->ProductsID))
                throw new \RequiredArgumentMissingException("Online Booking create was missing a ProductsID!");
            if (!isset($booking->HeatMainID))
                throw new \RequiredArgumentMissingException("Online Booking create was missing a HeatMainID!");
            
            $product = $logic->products->get($booking->ProductsID); // exception will be thrown if not found
            $product = $product[0];
            if ($product->ProductType != Enums::PRODUCT_TYPE_RESERVATION)
                throw new \CSException("Online booking create attempted to use a non-reservation product type! Received product type #" . $product->ProductType);

            $heatMain = $logic->heatMain->get($booking->HeatMainID); // exception will be thrown if not found
            $heatMain = $heatMain[0];
            if ($product->R_Points < $heatMain->PointsNeeded)
                throw new \CSException("Online booking create attempted to use a heat which requires more points(" . $heatMain->PointsNeeded . ") than the product supplies(" . $product->R_Points . ")!");
            
            return $booking;
        });
    }

    public function update(/* $id, $params = array() */) {
        $args = func_get_args();
        $db =& $this->db;
        $logic =& $this->logic;
        $closure = function($old, $new) use (&$db, &$logic) {
            if (isset($new->ProductsID) || isset($new->HeatMainID)) {
                // we are updating either the product or the heatmain id
                $product = $logic->products->get($new->ProductsID);
                $product = $product[0];
                if ($product->ProductType != Enums::PRODUCT_TYPE_RESERVATION)
                    throw new \CSException("Online booking update attempted to use a non-reservation product type! Received product type #" . $product->ProductType);

                $heatMain = $logic->heatMain->get($new->HeatMainID);
                $heatMain = $heatMain[0];
                if ($product->R_Points < $heatMain->PointsNeeded)
                    throw new \CSException("Online booking update attempted to use a heat which requires more points(" . $heatMain->PointsNeeded . ") than the product supplies(" . $product->R_Points . ")!");
            }
            return $new;
        };
        array_push($args, $closure);
        return call_user_func_array(array("parent", "update"), $args);
    }
}