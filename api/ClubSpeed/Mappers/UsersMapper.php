<?php

namespace ClubSpeed\Mappers;
use ClubSpeed\Utility\Arrays as Arrays;

class UsersMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'users';
        $this->register(array(
              'UserID'         => ''
            , 'FName'          => 'firstname' // to match the customers casing
            , 'LName'          => 'lastname'  // to match the customers casing
            , 'UserName'       => 'username'
            , 'Password'       => 'password'
            , 'CrdID'          => 'cardId'
            , 'Enabled'        => ''
            , 'EmailAddress'   => 'email'
            , 'PhoneNumber'    => 'phone'
            , 'Deleted'        => ''
            // , 'MaxHrsPerWeek'  => ''
            // , 'MaxHoursPerDay' => ''
            // , 'MondayOn'       => ''
            // , 'TuesdayOn'      => ''
            // , 'WednesdayOn'    => ''
            // , 'ThursdayOn'     => ''
            // , 'FridayOn'       => ''
            // , 'SaturdayOn'     => ''
            // , 'SundayOn'       => ''
            // , 'MondayStart'    => ''
            // , 'MondayEnd'      => ''
            // , 'TuesdayStart'   => ''
            // , 'TuesdayEnd'     => ''
            // , 'WednesdayStart' => ''
            // , 'WednesdayEnd'   => ''
            // , 'ThursdayStart'  => ''
            // , 'ThursdayEnd'    => ''
            // , 'FridayStart'    => ''
            // , 'FridayEnd'      => ''
            // , 'SaturdayStart'  => ''
            // , 'SaturdayEnd'    => ''
            // , 'SundayStart'    => ''
            // , 'SundayEnd'      => ''
            , 'EmpStartDate'   => ''
            , 'WebPassword'    => ''
            , 'SystemUsers'    => 'isSystemUser'
            // , 'SystemUsers'    => '' // or keep this?
        ));

        $this->restrict('client', array(
            'password',
            'webPassword'
        ));
    }
}