<?php

namespace ClubSpeed\Database\Records;

require_once(__DIR__.'/DbRecord.php');

class OnlineBookingReservations extends DbRecord {

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

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    // $this->OnlineBookingReservationsID  = isset($data['OnlineBookingReservationsID'])   ? $data['OnlineBookingReservationsID']  : @$data['onlineBookingReservationsID'];
                    // $this->OnlineBookingsID             = isset($data['OnlineBookingsID'])              ? $data['OnlineBookingsID']             : @$data['onlineBookingsId'];
                    // $this->CustomersID                  = isset($data['CustomersID'])                   ? $data['CustomersID']                  : @$data['customersId'];
                    // $this->SessionID                    = isset($data['SessionID'])                     ? $data['SessionID']                    : @$data['sessionId'];
                    // $this->Quantity                     = isset($data['Quantity'])                      ? $data['Quantity']                     : @$data['quantity'];
                    // $this->CreatedAt                    = isset($data['CreatedAt'])                     ? $data['CreatedAt']                    : @$data['createdAt'];
                    // $this->ExpiresAt                    = isset($data['ExpiresAt'])                     ? $data['ExpiresAt']                    : @$data['expiresAt'];
                    // $this->_convert();
                    if (isset($data['OnlineBookingReservationsID']))    $this->OnlineBookingReservationsID  = \ClubSpeed\Utility\Convert::toNumber($data['OnlineBookingReservationsID']);
                    if (isset($data['OnlineBookingsID']))               $this->OnlineBookingsID             = \ClubSpeed\Utility\Convert::toNumber($data['OnlineBookingsID']);
                    if (isset($data['CustomersID']))                    $this->CustomersID                  = \ClubSpeed\Utility\Convert::toNumber($data['CustomersID']);
                    if (isset($data['SessionID']))                      $this->SessionID                    = \ClubSpeed\Utility\Convert::toString($data['SessionID']);
                    if (isset($data['Quantity']))                       $this->Quantity                     = \ClubSpeed\Utility\Convert::toNumber($data['Quantity']);
                    if (isset($data['CreatedAt']))                      $this->CreatedAt                    = \ClubSpeed\Utility\Convert::toString($data['CreatedAt']);
                    if (isset($data['ExpiresAt']))                      $this->ExpiresAt                    = \ClubSpeed\Utility\Convert::toString($data['ExpiresAt']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    protected function _convert() {
        $this->OnlineBookingReservationsID  = \ClubSpeed\Utility\Convert::toNumber($this->OnlineBookingReservationsID);
        $this->OnlineBookingsID             = \ClubSpeed\Utility\Convert::toNumber($this->OnlineBookingsID);
        $this->CustomersID                  = \ClubSpeed\Utility\Convert::toNumber($this->CustomersID);
        $this->SessionID                    = \ClubSpeed\Utility\Convert::toString($this->SessionID);
        $this->Quantity                     = \ClubSpeed\Utility\Convert::toNumber($this->Quantity);
        $this->CreatedAt                    = \ClubSpeed\Utility\Convert::toString($this->CreatedAt);
        $this->ExpiresAt                    = \ClubSpeed\Utility\Convert::toString($this->ExpiresAt);
    }

    public function toJson() {
        return array(
              'onlineBookingReservationsID' => $this->OnlineBookingReservationsID
            , 'onlineBookingsId'            => $this->OnlineBookingsID
            , 'customersId'                 => $this->CustomersID
            , 'sessionId'                   => $this->SessionID
            , 'quantity'                    => $this->Quantity
            , 'createdAt'                   => $this->CreatedAt
            , 'expiresAt'                   => $this->ExpiresAt
        );
    }

    public function validate($type = "") {
        switch (strtolower($type)) {
            case 'insert':
                if (is_null($this->OnlineBookingsID))
                    throw new \InvalidArgumentException("Create reservation for online booking requires an onlineBookingsId!");
                if (is_null($this->Quantity) || !is_int($this->Quantity) || $this->Quantity < 1)
                    throw new \InvalidArgumentException("Create reservation for online booking requires a quantity greater than 0! Received: " . $quantity);
                if (is_null($this->SessionID) || empty($this->SessionID))
                    throw new \InvalidArgumentException("Create reservation for online booking requires a sessionId!");
                break;
        }
    }
}