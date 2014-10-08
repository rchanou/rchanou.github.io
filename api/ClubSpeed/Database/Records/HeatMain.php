<?php

namespace ClubSpeed\Database\Records;

class HeatMain extends BaseRecord {

    public static $table      = 'dbo.HeatMain';
    public static $tableAlias = 'htmn';
    public static $key        = 'HeatNo';

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

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['HeatNo']))                     $this->HeatNo                   = \ClubSpeed\Utility\Convert::toNumber          ($data['HeatNo']);
                    if (isset($data['TrackNo']))                    $this->TrackNo                  = \ClubSpeed\Utility\Convert::toString          ($data['TrackNo']);
                    if (isset($data['ScheduledTime']))              $this->ScheduledTime            = \ClubSpeed\Utility\Convert::toDateForServer   ($data['ScheduledTime']);
                    if (isset($data['HeatTypeNo']))                 $this->HeatTypeNo               = \ClubSpeed\Utility\Convert::toNumber          ($data['HeatTypeNo']);
                    if (isset($data['LapsOrMinutes']))              $this->LapsOrMinutes            = \ClubSpeed\Utility\Convert::toNumber          ($data['LapsOrMinutes']);
                    if (isset($data['HeatStatus']))                 $this->HeatStatus               = \ClubSpeed\Utility\Convert::toNumber          ($data['HeatStatus']);
                    if (isset($data['EventRound']))                 $this->EventRound               = \ClubSpeed\Utility\Convert::toNumber          ($data['EventRound']);
                    if (isset($data['Begining']))                   $this->Begining                 = \ClubSpeed\Utility\Convert::toDateForServer   ($data['Begining']);
                    if (isset($data['Finish']))                     $this->Finish                   = \ClubSpeed\Utility\Convert::toDateForServer   ($data['Finish']);
                    if (isset($data['WinBy']))                      $this->WinBy                    = \ClubSpeed\Utility\Convert::toNumber          ($data['WinBy']);
                    if (isset($data['RaceBy']))                     $this->RaceBy                   = \ClubSpeed\Utility\Convert::toNumber          ($data['RaceBy']);
                    if (isset($data['ScheduleDuration']))           $this->ScheduleDuration         = \ClubSpeed\Utility\Convert::toNumber          ($data['ScheduleDuration']);
                    if (isset($data['PointsNeeded']))               $this->PointsNeeded             = \ClubSpeed\Utility\Convert::toNumber          ($data['PointsNeeded']);
                    if (isset($data['SpeedLevel']))                 $this->SpeedLevel               = \ClubSpeed\Utility\Convert::toNumber          ($data['SpeedLevel']);
                    if (isset($data['HeatColor']))                  $this->HeatColor                = \ClubSpeed\Utility\Convert::toNumber          ($data['HeatColor']);
                    if (isset($data['NumberOfReservation']))        $this->NumberOfReservation      = \ClubSpeed\Utility\Convert::toNumber          ($data['NumberOfReservation']);
                    if (isset($data['MemberOnly']))                 $this->MemberOnly               = \ClubSpeed\Utility\Convert::toBoolean         ($data['MemberOnly']);
                    if (isset($data['HeatNotes']))                  $this->HeatNotes                = \ClubSpeed\Utility\Convert::toString          ($data['HeatNotes']);
                    if (isset($data['ScoreID']))                    $this->ScoreID                  = \ClubSpeed\Utility\Convert::toNumber          ($data['ScoreID']);
                    if (isset($data['RacersPerHeat']))              $this->RacersPerHeat            = \ClubSpeed\Utility\Convert::toNumber          ($data['RacersPerHeat']);
                    if (isset($data['NumberOfCadetReservation']))   $this->NumberOfCadetReservation = \ClubSpeed\Utility\Convert::toNumber          ($data['NumberOfCadetReservation']);
                    if (isset($data['CadetsPerHeat']))              $this->CadetsPerHeat            = \ClubSpeed\Utility\Convert::toNumber          ($data['CadetsPerHeat']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        switch (strtolower($type)) {
            case 'insert':
                
                break;
        }
    }
}