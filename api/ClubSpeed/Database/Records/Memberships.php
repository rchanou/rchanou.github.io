<?php

namespace ClubSpeed\Database\Records;

class Memberships extends BaseRecord {
    protected static $_definition;

    public $CustID;
    public $MembershipTypeID;
    public $LastChanged;
    public $ExpirationDate;
    public $Notes;
    public $ByUserID;
}