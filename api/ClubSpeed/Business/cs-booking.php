<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'../../Utility/Convert.php');

/**
 * The business logic class
 * for ClubSpeed online booking.
 */
class CSBooking {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSBooking class.
     *
     * The CSBooking constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
    }

    /**
     * Document: TODO
     */
    public final function create($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->onlineBookings->map('server', $params);
        $onlineBookingsId = $this->db->onlineBookings->create($mapped);
        return array(
            "onlineBookingsId" => $onlineBookingsId
        );
    }

    /**
     * Document: TODO
     */
    public final function all() {
        $all = $this->db->onlineBookingAvailability->all();
        $compressed = $this->db->onlineBookingAvailability->compress($all);
        return $compressed;
    }

    public final function get($onlineBookingsId) {
        $get = $this->db->onlineBookingAvailability->get($onlineBookingsId);
        $compressed = $this->db->onlineBookingAvailability->compress($get);
        return $compressed;
    }

    public final function find($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->onlineBookingAvailability->map('server', $params);
        $find = $this->db->onlineBookingAvailability->find($mapped);
        $compressed = $this->db->onlineBookingAvailability->compress($find);
        return $compressed;
    }

    public final function update($onlineBookingsId, $params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $booking = $this->db->onlineBookings->get($onlineBookingsId);
        if (is_null($booking))
            throw new \RecordNotFoundException("Attempted to update a non-existent online booking! Received onlineBookingsId: " . $onlineBookingsId);
        $booking = $this->db->onlineBookings->blank();
        $booking->load($onlineBookingsId);
        $mapped = $this->db->onlineBookings->map('server', $params);
        $booking->load($mapped);
        return $this->db->onlineBookings->update($booking);
    }

    public final function delete($onlineBookingsId) {
        return $this->db->onlineBookings->delete($onlineBookingsId);
    }
}