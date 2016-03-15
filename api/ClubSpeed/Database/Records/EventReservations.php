<?php

namespace ClubSpeed\Database\Records;

class EventReservations extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $AllowOnlineReservation;
    public $CheckID;
    public $CustID;
    public $CustomerName;
    public $Deleted;
    public $Description;
    public $EndTime;
    public $EventTypeID;
    public $IsEventClosure;
    public $IsMixed;
    public $Label;
    public $MainID;
    public $MinNoOfAdultsPerBooking;
    public $MinNoOfCadetsPerBooking;
    public $NoOfCadetRacers;
    public $NoOfRacers;
    public $NoOfTotalRacers;
    public $Notes;
    public $PtsPerReservation;
    public $RepID;
    public $StartTime;
    public $Status;
    public $Subject;
    public $TypeID;
    public $UserID;
}