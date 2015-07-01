<?php

namespace ClubSpeed\Database\Records;

class MembershipTypes extends BaseRecord {
    protected static $_definition;

    public $MembershipTypeID;
    public $Description;
    public $IsExpires;
    public $Seq;
    public $Enabled;
    public $ExpirationType;
    public $PriceLevel;
}