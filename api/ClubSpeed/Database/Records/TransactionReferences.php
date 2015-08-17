<?php

namespace ClubSpeed\Database\Records;

class TransactionReferences extends BaseRecord {
    protected static $_definition; // must be declared, so BaseRecord can use it in definition()
    
    public $TransactionReferencesID;
    public $CheckID;
    // public $TransactionID;
    public $TransactionReference;
    public $Amount;
    public $Currency;
    // public $Created;
}