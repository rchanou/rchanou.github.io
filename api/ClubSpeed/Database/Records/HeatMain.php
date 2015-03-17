<?php

namespace ClubSpeed\Database\Records;

class HeatMain extends BaseRecord {
    protected static $_definition;

    public $HeatNo;
    public $TrackNo;
    public $ScheduledTime;
    public $HeatTypeNo;
    public $LapsOrMinutes;
    public $HeatStatus;
    public $EventRound;
    public $Begining;
    public $Finish;
    public $WinBy;
    public $RaceBy;
    public $ScheduleDuration;
    public $PointsNeeded;
    public $SpeedLevel;
    public $HeatColor;
    public $NumberOfReservation;
    public $MemberOnly;
    public $HeatNotes;
    public $ScoreID;
    public $RacersPerHeat;
    public $NumberOfCadetReservation;
    public $CadetsPerHeat;
}