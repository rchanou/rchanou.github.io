<?php

namespace ClubSpeed\Database\Records;

class GiftCardHistory extends BaseRecord {
    protected static $_definition;

    public $HistoryID;
    public $CustID;
    public $UserID;
    public $Points;
    public $Type;
    public $Notes;
    public $CheckID;
    public $CheckDetailID;
    public $IPAddress;
    public $TransactionDate;
    public $EurekasDBName;
    public $EurekasCheckID;
    public $EurekasPaidInvoice;
}