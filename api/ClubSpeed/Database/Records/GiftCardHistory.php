<?php

namespace ClubSpeed\Database\Records;

class GiftCardHistory extends BaseRecord {

    public static $table      = 'dbo.GiftCardHistory';
    public static $tableAlias = 'gch';
    public static $key        = 'HistoryID';

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
    
    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['HistoryID']))          $this->HistoryID          = \ClubSpeed\Utility\Convert::toNumber          ($data['HistoryID']);
                    if (isset($data['CustID']))             $this->CustID             = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['UserID']))             $this->UserID             = \ClubSpeed\Utility\Convert::toNumber          ($data['UserID']);
                    if (isset($data['Points']))             $this->Points             = \ClubSpeed\Utility\Convert::toNumber          ($data['Points']);
                    if (isset($data['Type']))               $this->Type               = \ClubSpeed\Utility\Convert::toNumber          ($data['Type']);
                    if (isset($data['Notes']))              $this->Notes              = \ClubSpeed\Utility\Convert::toString          ($data['Notes']);
                    if (isset($data['CheckID']))            $this->CheckID            = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckID']);
                    if (isset($data['CheckDetailID']))      $this->CheckDetailID      = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailID']);
                    if (isset($data['IPAddress']))          $this->IPAddress          = \ClubSpeed\Utility\Convert::toString          ($data['IPAddress']);
                    if (isset($data['TransactionDate']))    $this->TransactionDate    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['TransactionDate']);
                    if (isset($data['EurekasDBName']))      $this->EurekasDBName      = \ClubSpeed\Utility\Convert::toString          ($data['EurekasDBName']);
                    if (isset($data['EurekasCheckID']))     $this->EurekasCheckID     = \ClubSpeed\Utility\Convert::toNumber          ($data['EurekasCheckID']);
                    if (isset($data['EurekasPaidInvoice'])) $this->EurekasPaidInvoice = \ClubSpeed\Utility\Convert::toString          ($data['EurekasPaidInvoice']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        // todo
    }
}