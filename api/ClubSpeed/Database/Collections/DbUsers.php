<?php

namespace ClubSpeed\Database\Collections;

class DbUsers extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Users');
        parent::__construct($db);
        // $this->dbToJson = array(
        //       "UserID"          => "userId"
        //     , "FName"           => "firstName"
        //     , "LName"           => "lastName"
        //     , "UserName"        => "username"
        //     , "Password"        => "password"
        //     , "CrdID"           => "cardId"
        //     , "Enabled"         => "enabled"
        //     , "EmailAddress"    => "email"
        //     , "PhoneNumber"     => "phone"
        //     , "Deleted"         => "deleted"
        //     , "MaxHrsPerWeek"   => "maxHoursPerWeek"
        //     , "MaxHoursPerDay"  => "maxHoursPerDay"
        //     , "MondayOn"        => "mondayOn"
        //     , "TuesdayOn"       => "tuesdayOn"
        //     , "WednesdayOn"     => "wednesdayOn"
        //     , "ThursdayOn"      => "thursdayOn"
        //     , "FridayOn"        => "fridayOn"
        //     , "SaturdayOn"      => "saturdayOn"
        //     , "SundayOn"        => "sundayOn"
        //     , "MondayStart"     => "mondayStart"
        //     , "MondayEnd"       => "mondayEnd"
        //     , "TuesdayStart"    => "tuesdayStart"
        //     , "TuesdayEnd"      => "tuesdayEnd"
        //     , "WednesdayStart"  => "wednesdayStart"
        //     , "WednesdayEnd"    => "wednesdayEnd"
        //     , "ThursdayStart"   => "thursdayStart"
        //     , "ThursdayEnd"     => "thursdayEnd"
        //     , "FridayStart"     => "fridayStart"
        //     , "FridayEnd"       => "fridayEnd"
        //     , "SaturdayStart"   => "saturdayStart"
        //     , "SaturdayEnd"     => "saturdayEnd"
        //     , "SundayStart"     => "sundayStart"
        //     , "SundayEnd"       => "sundayEnd"
        //     , "EmpStartDate"    => "employeeStartDate"
        //     , "WebPassword"     => "webPassword"
        //     , "SystemUsers"     => "systemUsers"
        // );
        // parent::secondaryInit();
    }
}