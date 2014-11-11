<?php

namespace ClubSpeed\Logic;

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
        return $this->_create($params, function($booking) {
            if (!isset($booking->IsPublic))
                $booking->IsPublic = true; // default to public visibility
            if ($booking->QuantityTotal <= 0)
                $booking->QuantityTotal = 0; // disallow negatives
            return $booking;
        });
    }
}