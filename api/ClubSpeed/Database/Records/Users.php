<?php

namespace ClubSpeed\Database\Records;

class Users extends BaseRecord {
    protected static $_definition;

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
}