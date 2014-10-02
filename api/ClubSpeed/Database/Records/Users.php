<?php

namespace ClubSpeed\Database\Records;

class Users extends BaseRecord {

    public static $table = 'dbo.Users';
    public static $tableAlias = 'u';
    public static $key = 'UserID';

    public $UserID;
    public $FName;
    public $LName;
    public $UserName;
    public $Password;
    public $CrdID;
    public $Enabled;
    public $EmailAddress;
    public $PhoneNumber;
    public $Deleted;
    public $MaxHrsPerWeek;
    public $MaxHoursPerDay;
    public $MondayOn;
    public $TuesdayOn;
    public $WednesdayOn;
    public $ThursdayOn;
    public $FridayOn;
    public $SaturdayOn;
    public $SundayOn;
    public $MondayStart;
    public $MondayEnd;
    public $TuesdayStart;
    public $TuesdayEnd;
    public $WednesdayStart;
    public $WednesdayEnd;
    public $ThursdayStart;
    public $ThursdayEnd;
    public $FridayStart;
    public $FridayEnd;
    public $SaturdayStart;
    public $SaturdayEnd;
    public $SundayStart;
    public $SundayEnd;
    public $EmpStartDate;
    public $WebPassword;
    public $SystemUsers;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['UserID']))             $this->UserID           = \ClubSpeed\Utility\Convert::toNumber          ($data['UserID']);
                    if (isset($data['FName']))              $this->FName            = \ClubSpeed\Utility\Convert::toString          ($data['FName']);
                    if (isset($data['LName']))              $this->LName            = \ClubSpeed\Utility\Convert::toString          ($data['LName']);
                    if (isset($data['UserName']))           $this->UserName         = \ClubSpeed\Utility\Convert::toString          ($data['UserName']);
                    if (isset($data['Password']))           $this->Password         = \ClubSpeed\Utility\Convert::toString          ($data['Password']);
                    if (isset($data['CrdID']))              $this->CrdID            = \ClubSpeed\Utility\Convert::toNumber          ($data['CrdID']);
                    if (isset($data['Enabled']))            $this->Enabled          = \ClubSpeed\Utility\Convert::toNumber          ($data['Enabled']);
                    if (isset($data['EmailAddress']))       $this->EmailAddress     = \ClubSpeed\Utility\Convert::toString          ($data['EmailAddress']);
                    if (isset($data['PhoneNumber']))        $this->PhoneNumber      = \ClubSpeed\Utility\Convert::toString          ($data['PhoneNumber']);
                    if (isset($data['Deleted']))            $this->Deleted          = \ClubSpeed\Utility\Convert::toBoolean         ($data['Deleted']);
                    if (isset($data['MaxHrsPerWeek']))      $this->MaxHrsPerWeek    = \ClubSpeed\Utility\Convert::toNumber          ($data['MaxHrsPerWeek']);
                    if (isset($data['MaxHoursPerDay']))     $this->MaxHoursPerDay   = \ClubSpeed\Utility\Convert::toNumber          ($data['MaxHoursPerDay']);
                    if (isset($data['MondayOn']))           $this->MondayOn         = \ClubSpeed\Utility\Convert::toBoolean         ($data['MondayOn']);
                    if (isset($data['TuesdayOn']))          $this->TuesdayOn        = \ClubSpeed\Utility\Convert::toBoolean         ($data['TuesdayOn']);
                    if (isset($data['WednesdayOn']))        $this->WednesdayOn      = \ClubSpeed\Utility\Convert::toBoolean         ($data['WednesdayOn']);
                    if (isset($data['ThursdayOn']))         $this->ThursdayOn       = \ClubSpeed\Utility\Convert::toBoolean         ($data['ThursdayOn']);
                    if (isset($data['FridayOn']))           $this->FridayOn         = \ClubSpeed\Utility\Convert::toBoolean         ($data['FridayOn']);
                    if (isset($data['SaturdayOn']))         $this->SaturdayOn       = \ClubSpeed\Utility\Convert::toBoolean         ($data['SaturdayOn']);
                    if (isset($data['SundayOn']))           $this->SundayOn         = \ClubSpeed\Utility\Convert::toBoolean         ($data['SundayOn']);
                    if (isset($data['MondayStart']))        $this->MondayStart      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['MondayStart']);
                    if (isset($data['MondayEnd']))          $this->MondayEnd        = \ClubSpeed\Utility\Convert::toDateForServer   ($data['MondayEnd']);
                    if (isset($data['TuesdayStart']))       $this->TuesdayStart     = \ClubSpeed\Utility\Convert::toDateForServer   ($data['TuesdayStart']);
                    if (isset($data['TuesdayEnd']))         $this->TuesdayEnd       = \ClubSpeed\Utility\Convert::toDateForServer   ($data['TuesdayEnd']);
                    if (isset($data['WednesdayStart']))     $this->WednesdayStart   = \ClubSpeed\Utility\Convert::toDateForServer   ($data['WednesdayStart']);
                    if (isset($data['WednesdayEnd']))       $this->WednesdayEnd     = \ClubSpeed\Utility\Convert::toDateForServer   ($data['WednesdayEnd']);
                    if (isset($data['ThursdayStart']))      $this->ThursdayStart    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['ThursdayStart']);
                    if (isset($data['ThursdayEnd']))        $this->ThursdayEnd      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['ThursdayEnd']);
                    if (isset($data['FridayStart']))        $this->FridayStart      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['FridayStart']);
                    if (isset($data['FridayEnd']))          $this->FridayEnd        = \ClubSpeed\Utility\Convert::toDateForServer   ($data['FridayEnd']);
                    if (isset($data['SaturdayStart']))      $this->SaturdayStart    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['SaturdayStart']);
                    if (isset($data['SaturdayEnd']))        $this->SaturdayEnd      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['SaturdayEnd']);
                    if (isset($data['SundayStart']))        $this->SundayStart      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['SundayStart']);
                    if (isset($data['SundayEnd']))          $this->SundayEnd        = \ClubSpeed\Utility\Convert::toDateForServer   ($data['SundayEnd']);
                    if (isset($data['EmpStartDate']))       $this->EmpStartDate     = \ClubSpeed\Utility\Convert::toDateForServer   ($data['EmpStartDate']);
                    if (isset($data['WebPassword']))        $this->WebPassword      = \ClubSpeed\Utility\Convert::toString          ($data['WebPassword']);
                    if (isset($data['SystemUsers']))        $this->SystemUsers      = \ClubSpeed\Utility\Convert::toBoolean         ($data['SystemUsers']);
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