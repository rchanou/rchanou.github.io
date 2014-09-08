<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'/../Utility/Convert.php');
require_once(__DIR__.'/../Utility/Convert.php');

/**
 * The business logic class
 * for ClubSpeed online booking.
 */
class CSReservations {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSReservations class.
     *
     * The CSReservations constructor requires an instantiated CSDatabase class for injection,
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
        $mapped = $this->db->onlineBookingReservations->map('server', $params);
        $reservation = $this->db->onlineBookingReservations->blank();
        $reservation->load($mapped);
        $availability = $this->db->onlineBookingAvailability->get($reservation->OnlineBookingsID);
        if (is_null($availability))
            throw new \OnlineBookingsNotFoundException("Create reservation for online booking attempted to use a non-existent onlineBookingsId! Received: " . $reservation->OnlineBookingsID);

        if($availability->ProductSpotsAvailableOnline < $reservation->Quantity)
            throw new \OnlineBookingsQuantityException("Create reservation attempted to use a quantity higher than what was available! Requested: " . $reservation->Quantity . " :: Available: " . $availability->ProductSpotsAvailableOnline);

        $onlineBookingReservationsId = $this->db->onlineBookingReservations->create($mapped);
        return array(
            "onlineBookingReservationsId" => $onlineBookingReservationsId
        );
    }

    public final function all() {
        $all = $this->db->onlineBookingReservations->all();
        $compressed = $this->db->onlineBookingReservations->compress($all);
        return $compressed;
    }

    public final function get($onlineBookingReservationsId) {
        $get = $this->db->onlineBookingReservations->get($onlineBookingReservationsId);
        $compressed = $this->db->onlineBookingReservations->compress($get);
        return $compressed;
    }

    public final function find($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->onlineBookingReservations->map('server', $params);
        $find = $this->db->onlineBookingReservations->find($mapped);
        $compressed = $this->db->onlineBookingReservations->compress($find);
        return $compressed;
    }

    /**
     * Document: TODO
     */
    public final function update($onlineBookingReservationsId, $params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $reservation = $this->db->onlineBookingReservations->get($onlineBookingReservationsId);
        if (is_null($reservation))
            throw new \RecordNotFoundException("Attempted to update a non-existent online booking reservation! Received onlineBookingReservationsId: " . $onlineBookingReservationsId);
        $reservation = $this->db->onlineBookingReservations->blank();
        $reservation->load($onlineBookingReservationsId);
        $mapped = $this->db->onlineBookingReservations->map('server', $params);
        $reservation->load($mapped);
        return $this->db->onlineBookingReservations->update($reservation);
    }

    /**
     * Document: TODO
     */
    public final function delete($onlineBookingReservationsId) {
        return $this->db->onlineBookingReservations->delete($onlineBookingReservationsId);
        // if (is_null($onlineBookingReservationsId))
        //     throw new \InvalidArgumentException("Delete reservation for online booking requires an onlineBookingReservationsId!");

        // $sql = "DELETE obr"
        //     ."\nFROM dbo.OnlineBookingReservations obr"
        //     ."\nWHERE obr.OnlineBookingReservationsID = :OnlineBookingReservationsID"
        //     ;
        // $params = array(
        //     ":OnlineBookingReservationsID" => $onlineBookingReservationsId
        // );
        // $affected = $this->db->exec($sql, $params); // check for a single delete
    }
}