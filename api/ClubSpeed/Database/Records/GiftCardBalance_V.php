<?php

namespace ClubSpeed\Database\Records;

class GiftCardBalance_V extends BaseRecord {

    public static $table      = 'dbo.GiftCardBalance_V';
    public static $tableAlias = 'gchsv';
    public static $key        = 'CrdID'; // consider this to be the unique key? we don't have a guarantee, technically

    public $CustID;
    public $CrdID;
    public $Balance;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CustID']))         $this->CustID   = \ClubSpeed\Utility\Convert::toNumber ($data['CustID']);
                    if (isset($data['CrdID']))          $this->CrdID    = \ClubSpeed\Utility\Convert::toNumber ($data['CrdID']);
                    if (isset($data['Balance']))        $this->Balance  = \ClubSpeed\Utility\Convert::toNumber ($data['Balance']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}