<?php

namespace ClubSpeed\Database\Records;

class TriggerLogs extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $CustID;
    public $LastUpdated;
    public $TableName;
    public $Type;
    public $Deleted;
}