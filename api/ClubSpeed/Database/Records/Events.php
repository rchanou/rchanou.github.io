<?php

namespace ClubSpeed\Database\Records;

class Events extends BaseRecord {
    protected static $_definition;

    public $EventID;
    public $EventTypeID;
    public $MemberOnly;
    public $EventTheme;
    public $TotalRacers;
    public $EventDesc;
    public $EventTypeName;
    public $EventDuration;
    public $EventScheduledTime;
    public $DisplayAtRegistration;
    public $CheckID;
    public $IsEventClosure;
    public $RoundNum;
    public $EventNotes;
    public $ReservationID;
    public $OnlineCode;
    public $TrackNo;
    public $CreatedHeatSpots;
    public $CreatedHeatTime;
    public $TotalCadetRacers;
}