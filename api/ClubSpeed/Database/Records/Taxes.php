<?php

namespace ClubSpeed\Database\Records;

class Taxes extends BaseRecord {
    protected static $_definition;

    public $TaxID;
    public $Description;
    public $Amount;
    public $Deleted;
    public $GST;
}