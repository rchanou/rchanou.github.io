<?php

namespace ClubSpeed\Database\Records;

class PointHistory extends BaseRecord {

    public static $table      = 'dbo.PointHistory';
    public static $tableAlias = 'pnthstry';
    public static $key        = 'PointHistoryID';

    public $PointHistoryID;
    public $CustID;
    public $CheckID;
    public $UserID;
    public $ReferenceID;
    public $PointAmount;
    public $Type;
    public $PointDate;
    public $PointExpDate;
    public $Notes;
    public $RefPointHistoryID;
    public $IPAddress;
    public $IsManual;
    public $CheckDetailID;
    public $ReservationID;
    public $Username;
    public $ApprovedByUserName;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['PointHistoryID']))     $this->PointHistoryID     = \ClubSpeed\Utility\Convert::toNumber          ($data['PointHistoryID']);
                    if (isset($data['CustID']))             $this->CustID             = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['CheckID']))            $this->CheckID            = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckID']);
                    if (isset($data['UserID']))             $this->UserID             = \ClubSpeed\Utility\Convert::toNumber          ($data['UserID']);
                    if (isset($data['ReferenceID']))        $this->ReferenceID        = \ClubSpeed\Utility\Convert::toNumber          ($data['ReferenceID']);
                    if (isset($data['PointAmount']))        $this->PointAmount        = \ClubSpeed\Utility\Convert::toNumber          ($data['PointAmount']);
                    if (isset($data['Type']))               $this->Type               = \ClubSpeed\Utility\Convert::toNumber          ($data['Type']);
                    if (isset($data['PointDate']))          $this->PointDate          = \ClubSpeed\Utility\Convert::toDateForServer   ($data['PointDate']);
                    if (isset($data['PointExpDate']))       $this->PointExpDate       = \ClubSpeed\Utility\Convert::toDateForServer   ($data['PointExpDate']);
                    if (isset($data['Notes']))              $this->Notes              = \ClubSpeed\Utility\Convert::toString          ($data['Notes']);
                    if (isset($data['RefPointHistoryID']))  $this->RefPointHistoryID  = \ClubSpeed\Utility\Convert::toNumber          ($data['RefPointHistoryID']);
                    if (isset($data['IPAddress']))          $this->IPAddress          = \ClubSpeed\Utility\Convert::toString          ($data['IPAddress']);
                    if (isset($data['IsManual']))           $this->IsManual           = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsManual']);
                    if (isset($data['CheckDetailID']))      $this->CheckDetailID      = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailID']);
                    if (isset($data['ReservationID']))      $this->ReservationID      = \ClubSpeed\Utility\Convert::toNumber          ($data['ReservationID']);
                    if (isset($data['Username']))           $this->Username           = \ClubSpeed\Utility\Convert::toString          ($data['Username']);
                    if (isset($data['ApprovedByUserName'])) $this->ApprovedByUserName = \ClubSpeed\Utility\Convert::toString          ($data['ApprovedByUserName']);
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
        }
    }
}