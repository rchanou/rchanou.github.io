<?php

namespace ClubSpeed\Database\Records;

class PrimaryCustomers_V extends BaseRecord {

    public static $table      = 'dbo.PrimaryCustomers_V';
    public static $tableAlias = 'pc';
    public static $key        = 'CustID';

    public $CustID;
    public $FName;
    public $LName;
    public $BirthDate;
    public $EmailAddress;
    public $ProSkill;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CustID']))         $this->CustID       = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['FName']))          $this->FName        = \ClubSpeed\Utility\Convert::toString          ($data['FName']);
                    if (isset($data['LName']))          $this->LName        = \ClubSpeed\Utility\Convert::toString          ($data['LName']);
                    if (isset($data['BirthDate']))      $this->BirthDate    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['BirthDate']);
                    if (isset($data['EmailAddress']))   $this->EmailAddress = \ClubSpeed\Utility\Convert::toString          ($data['EmailAddress']);
                    if (isset($data['ProSkill']))       $this->ProSkill     = \ClubSpeed\Utility\Convert::toNumber          ($data['ProSkill']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}