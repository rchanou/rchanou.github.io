<?php

namespace ClubSpeed\Database\Records;

class OnlineBookings extends BaseRecord {
    protected static $_definition;
    
    public $OnlineBookingsID;
    public $HeatMainID;
    public $ProductsID;
    public $IsPublic;
    public $QuantityTotal;
}