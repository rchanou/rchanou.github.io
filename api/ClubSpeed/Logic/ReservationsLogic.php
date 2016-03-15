<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Database\Helpers\UnitOfWork;

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
        $this->expire(); // call expire on any reservations construct (note: construct will not be hit unless reservations is attempted to be used)
    }

    public function create($params = array()) {
        $logic =& $this->logic;
        // note that in 5.4+, we can just reference $this inside the closure
        // and then $this can properly access private and protected items
        return parent::_create($params, function($reservation) use (&$logic) {
            if (empty($reservation->OnlineBookingsID))
                throw new \RequiredArgumentMissingException("Create reservation was missing OnlineBookingsID!");
            $availability = $logic->bookingAvailability->get($reservation->OnlineBookingsID);
            $availability = $availability[0];
            if($availability->ProductSpotsAvailableOnline < $reservation->Quantity)
                throw new \InvalidArgumentValueException("Create reservation attempted to use a quantity higher than what was available! Requested: " . $reservation->Quantity . " :: Available: " . $availability->ProductSpotsAvailableOnline);
            
            if ($reservation->Quantity < 1)
                throw new \InvalidArgumentValueException("Create reservation attempted to use a quantity less than 1! Received: " . $reservation->Quantity);

            $reservation->OnlineBookingReservationStatusID = 1; // hard coded to 1 (temporary) -- make a lookup from dbo.OnlineBookingReservationStatus eventually
            return $reservation;
        });
    }

    public function update(/* $id, $params = array() */) {
        $args = func_get_args();
        $logic =& $this->logic;
        $closure = function($old, $new) use (&$logic) {
            $availability = $logic->bookingAvailability->get($new->OnlineBookingsID);
            if (is_null($availability))
                throw new \CSException("Update reservation for online booking attempted to use a non-existent onlineBookingsId! Received: " . $reservation->OnlineBookingsID, 404);
            $availability = $availability[0];

            if ($new->Quantity < 1)
                throw new \InvalidArgumentValueException("Update reservation attempted to use a quantity less than 1! Received: " . $new->Quantity);

            if ($old->Quantity < $new->Quantity) {
                // attempting to increase quantity
                if ($availability->ProductSpotsAvailableOnline < $new->Quantity - $old->Quantity)
                    throw new \InvalidArgumentValueException("Update reservation attempted to use a quantity higher than what was available! Requested: " . $new->Quantity . " :: Available: " . ($availability->ProductSpotsAvailableOnline + $old->Quantity));
            }
            if ($new->OnlineBookingReservationStatusID === 2) // permanent -- MAKE THIS A LOOKUP LATER
                $new->ExpiresAt = Convert::toDateForServer('2038-01-18');
            
            return $new;
        };
        array_push($args, $closure);
        return call_user_func_array(array("parent", "update"), $args);
    }

    public function expire() {
        // cheat and just use a sql statement for performance purposes

        $statuses = $this->db->onlineBookingReservationStatus->match(array(
            'Status' => 'TEMPORARY'
        ));
        $status = $statuses[0];
        $statusId = $status->OnlineBookingReservationStatusID;
        $uow = $this->db->onlineBookingReservations->uow(
            UnitOfWork::build()
                ->action('all')
                ->where(array(
                    'OnlineBookingReservationStatusID' => $statusId,
                    'ExpiresAt' => array(
                        '$lt' => Convert::getDate()
                    )
                ))
        );
        $reservations = $uow->data;
        foreach($reservations as $reservation) {
            $reservationId = $reservation->OnlineBookingReservationsID;
            $this->db->onlineBookingReservations->delete($reservationId);
            $checkId = $reservation->CheckID;
            if (!is_null($checkId)) {
                $this->logic->checks->void($checkId);
            }
        }
    }
}
