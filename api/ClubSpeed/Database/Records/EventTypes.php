<?php

namespace ClubSpeed\Database\Records;

class EventTypes extends BaseRecord {
    protected static $_definition;

    public $EventTypeID;
    public $Deleted;
    public $Description;
    public $DisplayAtRegistration;
    public $Enabled;
    public $EventTypeName;
    public $EventTypeTheme;
    public $MemberOnly;
    public $OnlineProductID;
    public $PtsPerReservation;
    public $TrackNo;
}