<?php

namespace ClubSpeed\Database\Records;

class DiscountType extends BaseRecord {
    protected static $_definition;
    
    public $DiscountID;
    public $Description;
    public $CalculateType;
    public $Amount;
    public $Enabled;
    public $NeedApproved;
    public $ProductClassID;
    public $Deleted;
}