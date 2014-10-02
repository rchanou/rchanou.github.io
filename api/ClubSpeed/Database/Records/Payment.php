<?php

namespace ClubSpeed\Database\Records;

class Payment extends BaseRecord {

    public static $table      = 'dbo.Payment';
    public static $tableAlias = 'pmnt';
    public static $key        = 'PayID';

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

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['PayID']))                  $this->PayID                 = \ClubSpeed\Utility\Convert::toNumber         ($data['PayID']);
                    if (isset($data['CheckID']))                $this->CheckID               = \ClubSpeed\Utility\Convert::toNumber         ($data['CheckID']);
                    if (isset($data['UserID']))                 $this->UserID                = \ClubSpeed\Utility\Convert::toNumber         ($data['UserID']);
                    if (isset($data['PayTerminal']))            $this->PayTerminal           = \ClubSpeed\Utility\Convert::toString         ($data['PayTerminal']);
                    if (isset($data['Shift']))                  $this->Shift                 = \ClubSpeed\Utility\Convert::toNumber         ($data['Shift']);
                    if (isset($data['PayType']))                $this->PayType               = \ClubSpeed\Utility\Convert::toNumber         ($data['PayType']);
                    if (isset($data['PayDate']))                $this->PayDate               = \ClubSpeed\Utility\Convert::toDateForServer  ($data['PayDate']);
                    if (isset($data['PayStatus']))              $this->PayStatus             = \ClubSpeed\Utility\Convert::toString         ($data['PayStatus']);
                    if (isset($data['PayAmount']))              $this->PayAmount             = \ClubSpeed\Utility\Convert::toNumber         ($data['PayAmount']);
                    if (isset($data['PayTax']))                 $this->PayTax                = \ClubSpeed\Utility\Convert::toNumber         ($data['PayTax']);
                    if (isset($data['VoidDate']))               $this->VoidDate              = \ClubSpeed\Utility\Convert::toDateForServer  ($data['VoidDate']);
                    if (isset($data['VoidUser']))               $this->VoidUser              = \ClubSpeed\Utility\Convert::toNumber         ($data['VoidUser']);
                    if (isset($data['VoidTerminal']))           $this->VoidTerminal          = \ClubSpeed\Utility\Convert::toString         ($data['VoidTerminal']);
                    if (isset($data['VoidNotes']))              $this->VoidNotes             = \ClubSpeed\Utility\Convert::toString         ($data['VoidNotes']);
                    if (isset($data['CheckNumber']))            $this->CheckNumber           = \ClubSpeed\Utility\Convert::toString         ($data['CheckNumber']); // this is nvarchar(10) on the database, not a typo
                    if (isset($data['CheckingAccountName']))    $this->CheckingAccountName   = \ClubSpeed\Utility\Convert::toString         ($data['CheckingAccountName']);
                    if (isset($data['CreditCardNo']))           $this->CreditCardNo          = \ClubSpeed\Utility\Convert::toString         ($data['CreditCardNo']);
                    if (isset($data['CardType']))               $this->CardType              = \ClubSpeed\Utility\Convert::toString         ($data['CardType']);
                    if (isset($data['ExpirationDate']))         $this->ExpirationDate        = \ClubSpeed\Utility\Convert::toString         ($data['ExpirationDate']);
                    if (isset($data['AccountName']))            $this->AccountName           = \ClubSpeed\Utility\Convert::toString         ($data['AccountName']);
                    if (isset($data['Amount']))                 $this->Amount                = \ClubSpeed\Utility\Convert::toNumber         ($data['Amount']);
                    if (isset($data['ResponseTime']))           $this->ResponseTime          = \ClubSpeed\Utility\Convert::toNumber         ($data['ResponseTime']);
                    if (isset($data['AuthorizationCode']))      $this->AuthorizationCode     = \ClubSpeed\Utility\Convert::toString         ($data['AuthorizationCode']);
                    if (isset($data['AVS']))                    $this->AVS                   = \ClubSpeed\Utility\Convert::toString         ($data['AVS']);
                    if (isset($data['ReferenceNumber']))        $this->ReferenceNumber       = \ClubSpeed\Utility\Convert::toString         ($data['ReferenceNumber']);
                    if (isset($data['ResultCode']))             $this->ResultCode            = \ClubSpeed\Utility\Convert::toString         ($data['ResultCode']);
                    if (isset($data['TroutD']))                 $this->TroutD                = \ClubSpeed\Utility\Convert::toString         ($data['TroutD']);
                    if (isset($data['TransactionDate']))        $this->TransactionDate       = \ClubSpeed\Utility\Convert::toDateForServer  ($data['TransactionDate']);
                    if (isset($data['AutAmount']))              $this->AutAmount             = \ClubSpeed\Utility\Convert::toNumber         ($data['AutAmount']);
                    if (isset($data['LastFour']))               $this->LastFour              = \ClubSpeed\Utility\Convert::toString         ($data['LastFour']);
                    if (isset($data['ExternalAccountNumber']))  $this->ExternalAccountNumber = \ClubSpeed\Utility\Convert::toString         ($data['ExternalAccountNumber']);
                    if (isset($data['ExternalAccountName']))    $this->ExternalAccountName   = \ClubSpeed\Utility\Convert::toString         ($data['ExternalAccountName']);
                    if (isset($data['VID']))                    $this->VID                   = \ClubSpeed\Utility\Convert::toString         ($data['VID']);
                    if (isset($data['TransactionID']))          $this->TransactionID         = \ClubSpeed\Utility\Convert::toString         ($data['TransactionID']);
                    if (isset($data['BalanceRemaing']))         $this->BalanceRemaing        = \ClubSpeed\Utility\Convert::toNumber         ($data['BalanceRemaing']);
                    if (isset($data['CustID']))                 $this->CustID                = \ClubSpeed\Utility\Convert::toNumber         ($data['CustID']);
                    if (isset($data['VoucherID']))              $this->VoucherID             = \ClubSpeed\Utility\Convert::toNumber         ($data['VoucherID']);
                    if (isset($data['VoucherNotes']))           $this->VoucherNotes          = \ClubSpeed\Utility\Convert::toString         ($data['VoucherNotes']);
                    if (isset($data['HistoryID']))              $this->HistoryID             = \ClubSpeed\Utility\Convert::toNumber         ($data['HistoryID']);
                    if (isset($data['InvoicePaidHistoryID']))   $this->InvoicePaidHistoryID  = \ClubSpeed\Utility\Convert::toNumber         ($data['InvoicePaidHistoryID']);
                    if (isset($data['ExtCardType']))            $this->ExtCardType           = \ClubSpeed\Utility\Convert::toString         ($data['ExtCardType']);
                    if (isset($data['Tender']))                 $this->Tender                = \ClubSpeed\Utility\Convert::toNumber         ($data['Tender']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        switch (strtolower($type)) {
            case 'insert':
                break;
            case 'update':
                break;
        }
    }
}