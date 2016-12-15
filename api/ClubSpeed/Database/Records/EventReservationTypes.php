<?php

namespace ClubSpeed\Database\Records;

class EventReservationTypes extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $Description;
    // public $Picture; // don't collect this piece, same as Products.largeIcon
}
