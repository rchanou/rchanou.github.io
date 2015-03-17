<?php

namespace ClubSpeed\Database\Records;

class UserTasks_V extends BaseRecord {
    protected static $_definition;

    public $UserID;
    public $UserName;
    public $EmailAddress;
    public $RoleID;
    public $RoleName;
    public $TaskID;
    public $TaskDescription;
}