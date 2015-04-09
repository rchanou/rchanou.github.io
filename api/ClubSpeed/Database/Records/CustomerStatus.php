<?php

namespace ClubSpeed\Database\Records;

class CustomerStatus extends BaseRecord {
    protected static $_definition;

    public $StatusID;
    public $Description;
    public $Color;
    public $ShowOn1;
    public $ShowOn2;
    public $ShowOn3;
    public $ShowOn4;
    public $Deleted;
}