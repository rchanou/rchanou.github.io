<?php

namespace ClubSpeed\Database\Records;

class EventHeatDetails extends BaseRecord {
    protected static $_definition;

    public $EventID;
    public $CustID;
    public $RPM;
    public $DateAdded;
    public $RoundLoseNum;
    public $CheckID;
    public $TotalRacesInEvent;
}
