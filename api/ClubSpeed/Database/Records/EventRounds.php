<?php

namespace ClubSpeed\Database\Records;

class EventRounds extends BaseRecord {
    protected static $_definition;

    public $EventRound;
    public $CadetsPerHeat;
    public $EventID;
    public $GridID;
    public $HeatDescription;
    public $HeatsPerRacer;
    public $HeatTypeNo;
    public $IsInverted;
    public $IsSemi;
    public $LineUpType;
    public $ProceedMethod;
    public $ProceedValue;
    public $RacersPerHeat;
    public $RoundNum;
    public $RoundWinBy;
    public $ScoreSystemID;
}