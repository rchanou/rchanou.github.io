<?php

namespace ClubSpeed\Database\Records;

class UserTasks_V extends BaseRecord {

    public static $table      = 'dbo.UserTasks_V';
    public static $tableAlias = 'utv';
    public static $key        = array(
        'UserID' // not really a valid composite primary key!
        , 'RoleID' // UserID + RoleID is not distinct, due to cartesian product of TaskID left outer join!
        // TaskID -- don't include! Pagination is doing an inner join, and TaskID is based on a left outer join!
    );

    public $UserID;
    public $UserName;
    public $EmailAddress;
    public $RoleID;
    public $RoleName;
    public $TaskID;
    public $TaskDescription;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['UserID']))             $this->UserID          = \ClubSpeed\Utility\Convert::toNumber ($data['UserID']);
                    if (isset($data['UserName']))           $this->UserName        = \ClubSpeed\Utility\Convert::toString ($data['UserName']);
                    if (isset($data['EmailAddress']))       $this->EmailAddress    = \ClubSpeed\Utility\Convert::toString ($data['EmailAddress']);
                    if (isset($data['RoleID']))             $this->RoleID          = \ClubSpeed\Utility\Convert::toNumber ($data['RoleID']);
                    if (isset($data['RoleName']))           $this->RoleName        = \ClubSpeed\Utility\Convert::toString ($data['RoleName']);
                    if (isset($data['TaskID']))             $this->TaskID          = \ClubSpeed\Utility\Convert::toNumber ($data['TaskID']);
                    if (isset($data['TaskDescription']))    $this->TaskDescription = \ClubSpeed\Utility\Convert::toString ($data['TaskDescription']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}