<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'../../Utility/Convert.php');

/**
 * The business logic class
 * for ClubSpeed checks.
 */
class CSChecks {

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
        $mapped = $this->db->checks->map('server', $params);
        $checksId = $this->db->checks->create($mapped);
        return array(
            "checksId" => $checksId
        );
    }

    /**
     * Document: TODO
     */
    public final function all() {
        $all = $this->db->onlineBookingAvailability->all();
        $compressed = $this->db->checks->compress($all);
        return $compressed;
    }

    public final function get($checksId) {
        $get = $this->db->checks->get($checksId);
        $compressed = $this->db->checks->compress($get);
        return $compressed;
    }

    public final function find($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->checks->map('server', $params);
        $find = $this->db->checks->find($mapped);
        $compressed = $this->db->checks->compress($find);
        return $compressed;
    }

    public final function update($checksId, $params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $booking = $this->db->checks->get($checksId);
        if (is_null($booking))
            throw new \RecordNotFoundException("Attempted to update a non-existent online booking! Received checksId: " . $checksId);
        $booking = $this->db->checks->blank();
        $booking->load($checksId);
        $mapped = $this->db->checks->map('server', $params);
        $booking->load($mapped);
        return $this->db->checks->update($booking);
    }

    public final function delete($checksId) {
        return $this->db->checks->delete($checksId);
    }
}