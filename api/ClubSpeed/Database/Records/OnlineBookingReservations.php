<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Convert;

class OnlineBookingReservations extends BaseRecord {

    public static $table      = 'dbo.OnlineBookingReservations';
    public static $tableAlias = 'obr';
    public static $key        = 'OnlineBookingReservationsID';
    
    public $OnlineBookingReservationsID;
    public $OnlineBookingsID;
    public $CustomersID;
    public $SessionID;
    public $Quantity;
    public $CreatedAt;
    public $ExpiresAt;
    public $OnlineBookingReservationStatusID;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['OnlineBookingReservationsID']))        $this->OnlineBookingReservationsID      = \ClubSpeed\Utility\Convert::toNumber          ($data['OnlineBookingReservationsID']);
                    if (isset($data['OnlineBookingsID']))                   $this->OnlineBookingsID                 = \ClubSpeed\Utility\Convert::toNumber          ($data['OnlineBookingsID']);
                    if (isset($data['CustomersID']))                        $this->CustomersID                      = \ClubSpeed\Utility\Convert::toNumber          ($data['CustomersID']);
                    if (isset($data['SessionID']))                          $this->SessionID                        = \ClubSpeed\Utility\Convert::toString          ($data['SessionID']);
                    if (isset($data['Quantity']))                           $this->Quantity                         = \ClubSpeed\Utility\Convert::toNumber          ($data['Quantity']);
                    if (isset($data['CreatedAt']))                          $this->CreatedAt                        = \ClubSpeed\Utility\Convert::toDateForServer   ($data['CreatedAt']);
                    if (isset($data['ExpiresAt']))                          $this->ExpiresAt                        = \ClubSpeed\Utility\Convert::toDateForServer   ($data['ExpiresAt']);
                    if (isset($data['OnlineBookingReservationStatusID']))   $this->OnlineBookingReservationStatusID = \ClubSpeed\Utility\Convert::toNumber          ($data['OnlineBookingReservationStatusID']);
                }
            }
            else {
                $this->{self::$key} = Convert::toNumber($data);
            }
        }
    }

    public function validate($type = "") {
        // switch (strtolower($type)) {
        //     case 'insert':
        //         if (is_null($this->OnlineBookingsID))
        //             throw new \InvalidArgumentException("Create reservation for online booking requires an onlineBookingsId!");
        //         if (is_null($this->Quantity) || !is_int($this->Quantity) || $this->Quantity < 1)
        //             throw new \InvalidArgumentException("Create reservation for online booking requires a quantity greater than 0! Received: " . $this->Quantity);
        //         if (is_null($this->SessionID) || empty($this->SessionID))
        //             throw new \InvalidArgumentException("Create reservation for online booking requires a sessionId!");
        //         break;
        // }
    }
}