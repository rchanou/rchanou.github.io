<?php

namespace ClubSpeed\Database\Records;

class Tracks extends BaseRecord {
    protected static $_definition;

    public $TrackNo;
    public $Description;
    public $MainLoopID;
    public $PitEnterLoopID;
    public $AssignKartLoopID;
    public $PenaltyBoxLoopID;
    public $PrinterName;
    public $TrackLength;
    public $UnitLength;
    public $GridSize;
    public $AutoRun;
    public $SportID;
    public $AllowAddOnRacing;
}
