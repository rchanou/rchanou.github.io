<?php

namespace ClubSpeed\Database\Records;

class Taxes extends BaseRecord {

    public static $table      = 'dbo.Taxes';
    public static $tableAlias = 'txs';
    public static $key        = 'TaxID';
    
    public $TaxID;
    public $Description;
    public $Amount;
    public $Deleted;
    public $GST;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['TaxID']))          $this->TaxID       = \ClubSpeed\Utility\Convert::toNumber   ($data['TaxID']);
                    if (isset($data['Description']))    $this->Description = \ClubSpeed\Utility\Convert::toString   ($data['Description']);
                    if (isset($data['Amount']))         $this->Amount      = \ClubSpeed\Utility\Convert::toNumber   ($data['Amount']);
                    if (isset($data['Deleted']))        $this->Deleted     = \ClubSpeed\Utility\Convert::toBoolean  ($data['Deleted']);
                    if (isset($data['GST']))            $this->GST         = \ClubSpeed\Utility\Convert::toNumber   ($data['GST']);
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