<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed online booking.
 */
class ReservationsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ReservationsLogic class.
     *
     * The ReservationsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->onlineBookingReservations;

        $this->insertable = array(
              'ExpiresAt'
            , 'OnlineBookingsID'
            , 'Quantity'
            , 'SessionID'
            , 'CustomersID'
        );

        $this->updatable = array(
              'ExpiresAt'
            , 'OnlineBookingsID'
            , 'Quantity'
            , 'OnlineBookingReservationStatusID'
            , 'CustomersID'
        );

        $this->expire(); // call expire on any reservations construct (note: construct will not be hit unless reservations is attempted to be used)
    }

    public function create($params = array()) {
        $db =& $this->db;
        // note that in 5.4+, we can just reference $this inside the closure
        // and then $this can properly access private and protected items
        return parent::_create($params, function($reservation) use (&$db) {
            $reservation->validate('insert');
            $availability = $db->onlineBookingAvailability_V->get($reservation->OnlineBookingsID);
            if (is_null($availability))
                throw new \RecordNotFoundException("Create reservation for online booking attempted to use a non-existent onlineBookingsId! Received: " . $reservation->OnlineBookingsID);
            $availability = $availability[0];
            if($availability->ProductSpotsAvailableOnline < $reservation->Quantity)
                throw new \InvalidArgumentValueException("Create reservation attempted to use a quantity higher than what was available! Requested: " . $reservation->Quantity . " :: Available: " . $availability->ProductSpotsAvailableOnline);
            
            if ($reservation->Quantity < 1)
                throw new \InvalidArgumentValueException("Update reservation attempted to use a quantity less than 1! Received: " . $reservation->Quantity);

            $reservation->OnlineBookingReservationStatusID = 1; // hard coded to 1 (temporary) -- make a lookup from dbo.OnlineBookingReservationStatus eventually
            return $reservation;
        });
    }

    public function update(/* $id, $params = array() */) {
        $args = func_get_args();
        $db =& $this->db;
        $closure = function($old, $new) use (&$db) {
            $new->validate('update');
            $availability = $db->onlineBookingAvailability_V->get($new->OnlineBookingsID);
            if (is_null($availability))
                throw new \RecordNotFoundException("Update reservation for online booking attempted to use a non-existent onlineBookingsId! Received: " . $reservation->OnlineBookingsID);
            $availability = $availability[0];

            if ($new->Quantity < 1)
                throw new \InvalidArgumentValueException("Update reservation attempted to use a quantity less than 1! Received: " . $new->Quantity);

            if ($old->Quantity < $new->Quantity) {
                // attempting to increase quantity
                if ($availability->ProductSpotsAvailableOnline < $new->Quantity - $old->Quantity)
                    throw new \InvalidArgumentValueException("Update reservation attempted to use a quantity higher than what was available! Requested: " . $new->Quantity . " :: Available: " . $availability->ProductSpotsAvailableOnline);
            }
            if ($new->OnlineBookingReservationStatusID === 2) // permanent -- MAKE THIS A LOOKUP LATER
                $new->ExpiresAt = \ClubSpeed\Utility\Convert::toDateForServer('2038-01-18');
            
            return $new;
        };
        array_push($args, $closure);
        return call_user_func_array(array("parent", "update"), $args);
    }

    public function expire() {
        // cheat and just use a sql statement for performance purposes
        $sql = ""
            ."\nDELETE obr"
            ."\nFROM dbo.OnlineBookingReservations obr"
            ."\nINNER JOIN dbo.OnlineBookingReservationStatus obrs"
            ."\n    ON obr.OnlineBookingReservationStatusID = obrs.OnlineBookingReservationStatusID"
            ."\nWHERE"
            ."\n    obr.ExpiresAt < GETDATE()"
            ."\n    AND obrs.Status = 'TEMPORARY'"
            ;
        $this->db->exec($sql);
    }
}