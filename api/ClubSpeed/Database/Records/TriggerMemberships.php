<?php

namespace ClubSpeed\Database\Records;

class TriggerMemberships extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $CustID;
    public $MembershipTypeID;
    public $LastUpdated;
    public $IsDeleted;
    public $Deleted;
}