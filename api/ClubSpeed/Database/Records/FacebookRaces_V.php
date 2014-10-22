<?php

namespace ClubSpeed\Database\Records;

class FacebookRaces_V extends BaseRecord {

    public static $table      = 'dbo.FacebookRaces_V';
    public static $tableAlias = 'fbrv';
    public static $key        = 'CustID'; // there is no unique identifier -- will add if necessary

    public $CustID;
    public $Access_Token;
    public $HeatNo;
    public $HeatTypeName;
    public $FinishPosition;
    public $Finish;
    
    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CustID']))         $this->CustID         = \ClubSpeed\Utility\Convert::toNumber        ($data['CustID']);
                    if (isset($data['Access_Token']))   $this->Access_Token   = \ClubSpeed\Utility\Convert::toString        ($data['Access_Token']);
                    if (isset($data['HeatNo']))         $this->HeatNo         = \ClubSpeed\Utility\Convert::toNumber        ($data['HeatNo']);
                    if (isset($data['HeatTypeName']))   $this->HeatTypeName   = \ClubSpeed\Utility\Convert::toString        ($data['HeatTypeName']);
                    if (isset($data['FinishPosition'])) $this->FinishPosition = \ClubSpeed\Utility\Convert::toNumber        ($data['FinishPosition']);
                    if (isset($data['Finish']))         $this->Finish         = \ClubSpeed\Utility\Convert::toDateForServer ($data['Finish']);
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