<?php

namespace ClubSpeed\Database\Records;

class EventReservationLink extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $CheckID;
    public $ReservationID;
}