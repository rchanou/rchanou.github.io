<?php

namespace ClubSpeed\Database\Records;

class Checks extends BaseRecord {
    protected static $_definition;
    
    public $CheckID;
    public $CustID;
    public $CheckType;
    public $CheckStatus;
    public $CheckName;
    public $UserID;
    public $CheckTotal;
    public $BrokerName;
    public $Notes;
    public $Gratuity;
    public $Fee;
    public $OpenedDate;
    public $ClosedDate;
    public $IsTaxExempt;
    public $Discount;
    public $DiscountID;
    public $DiscountNotes;
    public $DiscountUserID;
    public $InvoiceDate;
}