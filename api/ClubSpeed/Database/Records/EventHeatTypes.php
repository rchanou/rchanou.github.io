<?php

namespace ClubSpeed\Database\Records;

class EventHeatTypes extends BaseRecord {
    protected static $_definition;

    public $EventHeatTypeNo;
    public $CadetsPerHeat;
    public $Description;
    public $EventTypeID;
    public $GridID;
    public $HeatsPerRacer;
    public $HeatTypeNo;
    public $IsInverted;
    public $IsSemi;
    public $LineUpType;
    public $ProceedMethod;
    public $ProceedValue;
    public $RacersPerHeat;
    public $RoundNum;
    public $RoundWinby;
    public $ScoreSystemID;
    public $TeamGroupingType;
}