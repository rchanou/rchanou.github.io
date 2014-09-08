<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/OnlineBookingReservations.php');

class DbOnlineBookingReservations extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookingReservations');
        $this->dbToJson = array(
            'OnlineBookingReservationsID'   => 'onlineBookingReservationsId'
            , 'OnlineBookingsID'            => 'onlineBookingsId'
            , 'CustomersID'                 => 'customersId'
            , 'SessionID'                   => 'sessionId'
            , 'Quantity'                    => 'quantity'
            , 'CreatedAt'                   => 'createdAt'
            , 'ExpiresAt'                   => 'expiresAt'
        );
        parent::secondaryInit();
    }

    public function compress($data = array()) {
        $table = 'reservations';
        $compressed = array(
            $table => array()
        );
        $inner =& $compressed[$table];
        if (isset($data) && !is_array($data))
            $data = array($data);
        foreach($data as $record) {
            if (!empty($record))
                $inner[] = $this->map('client', $record);
        }
        return $compressed;
    }
}