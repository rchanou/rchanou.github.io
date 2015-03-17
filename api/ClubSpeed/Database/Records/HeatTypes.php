<?php

namespace ClubSpeed\Database\Records;
use Clubspeed\Utility\Arrays;
use ClubSpeed\Utility\Types;

class HeatTypes extends BaseRecord {
    protected static $_definition;

    public $HeatTypeNo;
    public $TrackNo;
    public $HeatTypeName;
    public $WinBy;
    public $RaceBy;
    public $LapsOrMinutes;
    public $CannotBelow;
    public $CannotExceed;
    public $RacersPerHeat;
    public $ScheduleDuration;
    public $IsEventHeatOnly;
    public $SpeedLevel;
    public $IsPracticeHeat;
    public $Enabled;
    public $Deleted;
    public $Web;
    public $MemberOnly;
    public $OnHeatStart;
    public $OnHeatFinishAssignLoop;
    public $OnHeatStop;
    public $Cost;
    public $PrintResult;
    public $EntitleHeat;
    public $CadetsPerHeat;
}