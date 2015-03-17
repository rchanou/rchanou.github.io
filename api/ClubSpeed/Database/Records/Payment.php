<?php

namespace ClubSpeed\Database\Records;

class Payment extends BaseRecord {
    protected static $_definition;

    public $PayID;
    public $CheckID;
    public $UserID;
    public $PayTerminal;
    public $Shift;
    public $PayType;
    public $PayDate;
    public $PayStatus;
    public $PayAmount;
    public $PayTax;
    public $VoidDate;
    public $VoidUser;
    public $VoidTerminal;
    public $VoidNotes;
    public $CheckNumber;
    public $CheckingAccountName;
    public $CreditCardNo;
    public $CardType;
    public $ExpirationDate;
    public $AccountName;
    public $Amount;
    public $ResponseTime;
    public $AuthorizationCode;
    public $AVS;
    public $ReferenceNumber;
    public $ResultCode;
    public $TroutD;
    public $TransactionDate;
    public $AutAmount;
    public $LastFour;
    public $ExternalAccountNumber;
    public $ExternalAccountName;
    public $VID;
    public $TransactionID;
    public $BalanceRemaing;
    public $CustID;
    public $VoucherID;
    public $VoucherNotes;
    public $HistoryID;
    public $InvoicePaidHistoryID;
    public $ExtCardType;
    public $Tender;
}