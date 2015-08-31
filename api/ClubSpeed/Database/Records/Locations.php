<?php

namespace ClubSpeed\Database\Records;

class Locations extends BaseRecord {
    protected static $_definition;

    public $LocationID;
    public $LocationName;
    public $IPAddress;
    public $TimeoutMS;
}
