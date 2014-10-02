<?php

namespace ClubSpeed\Database\Records;

class Checks extends BaseRecord {

    public static $table      = 'dbo.Checks';
    public static $tableAlias = 'chcks';
    public static $key        = 'CheckID';
    
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

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CheckID']))            $this->CheckID          = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckID']);
                    if (isset($data['CustID']))             $this->CustID           = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['CheckType']))          $this->CheckType        = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckType']);
                    if (isset($data['CheckStatus']))        $this->CheckStatus      = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckStatus']);
                    if (isset($data['CheckName']))          $this->CheckName        = \ClubSpeed\Utility\Convert::toString          ($data['CheckName']);
                    if (isset($data['UserID']))             $this->UserID           = \ClubSpeed\Utility\Convert::toNumber          ($data['UserID']);
                    if (isset($data['CheckTotal']))         $this->CheckTotal       = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckTotal']);
                    if (isset($data['BrokerName']))         $this->BrokerName       = \ClubSpeed\Utility\Convert::toString          ($data['BrokerName']);
                    if (isset($data['Notes']))              $this->Notes            = \ClubSpeed\Utility\Convert::toString          ($data['Notes']);
                    if (isset($data['Gratuity']))           $this->Gratuity         = \ClubSpeed\Utility\Convert::toNumber          ($data['Gratuity']);
                    if (isset($data['Fee']))                $this->Fee              = \ClubSpeed\Utility\Convert::toNumber          ($data['Fee']);
                    if (isset($data['OpenedDate']))         $this->OpenedDate       = \ClubSpeed\Utility\Convert::toDateForServer   ($data['OpenedDate']);
                    if (isset($data['ClosedDate']))         $this->ClosedDate       = \ClubSpeed\Utility\Convert::toDateForServer   ($data['ClosedDate']);
                    if (isset($data['IsTaxExempt']))        $this->IsTaxExempt      = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsTaxExempt']);
                    if (isset($data['Discount']))           $this->Discount         = \ClubSpeed\Utility\Convert::toNumber          ($data['Discount']);
                    if (isset($data['DiscountID']))         $this->DiscountID       = \ClubSpeed\Utility\Convert::toNumber          ($data['DiscountID']);
                    if (isset($data['DiscountNotes']))      $this->DiscountNotes    = \ClubSpeed\Utility\Convert::toString          ($data['DiscountNotes']);
                    if (isset($data['DiscountUserID']))     $this->DiscountUserID   = \ClubSpeed\Utility\Convert::toNumber          ($data['DiscountUserID']);
                    if (isset($data['InvoiceDate']))        $this->InvoiceDate      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['InvoiceDate']);
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
            if (is_null($this->UserID) || !is_int($this->UserID))
                throw new \RequiredArgumentMissingException("Check create requires UserID to be an integer! Received: " . $this->UserID);
            if (is_null($this->CustID) || !is_int($this->CustID))
                throw new \RequiredArgumentMissingException("Check create requires CustID to be an integer! Received: " . $this->CustID);
                break;
            case 'update':
                // todo
                break;
        }
    }
}