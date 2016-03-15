<?php

namespace ClubSpeed\Database\Records;

class OnlineBookingReservations extends BaseRecord {
    protected static $_definition;
    
    public $OnlineBookingReservationsID;
    public $OnlineBookingsID;
    public $CustomersID;
    public $SessionID;
    public $Quantity;
    public $CreatedAt;
    public $ExpiresAt;
    public $OnlineBookingReservationStatusID;
    public $CheckID;
}
