<?php

namespace ClubSpeed\Database\Records;

class PrimaryCustomers_V extends BaseRecord {
    protected static $_definition;

    public $CustID;
    public $FName;
    public $LName;
    public $BirthDate;
    public $EmailAddress;
    public $ProSkill;
}