<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/OnlineBookingReservations.php');

class DbOnlineBookingReservations extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookingReservations');
        $this->dbToJson = array(
            'OnlineBookingReservationsID'   => 'onlineBookingReservationsID'
            , 'OnlineBookingsID'            => 'onlineBookingsId'
            , 'CustomersID'                 => 'customersId'
            , 'SessionID'                   => 'sessionId'
            , 'Quantity'                    => 'quantity'
            , 'CreatedAt'                   => 'createdAt'
            , 'ExpiresAt'                   => 'expiresAt'
        );
        $this->jsonToDb = array_flip($this->dbToJson);
    }

    /**
     * Document: TODO
     */
    public function compress($data = array()) {
        $return = array(
            'reservations' => array()
        );
        $reservations =& $return['reservations'];
        if (!is_array($data))
            $data = array($data);

        foreach($data as $reservation) {
            if (!empty($reservation))
                $reservations[] = $reservation->toJson();
        }
        return $return;
    }
}