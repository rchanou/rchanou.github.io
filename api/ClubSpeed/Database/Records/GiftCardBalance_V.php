<?php

namespace ClubSpeed\Database\Records;

class GiftCardBalance_V extends BaseRecord {

    public static $table      = 'dbo.GiftCardBalance_V';
    public static $tableAlias = 'gcbv';
    public static $key        = 'CrdID'; // consider this to be the unique key? we don't have a guarantee, technically

    public $CrdID;
    public $IsGiftCard;
    public $Money;
    public $Points;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CrdID']))          $this->CrdID        = \ClubSpeed\Utility\Convert::toNumber ($data['CrdID']);
                    if (isset($data['IsGiftCard']))     $this->IsGiftCard   = \ClubSpeed\Utility\Convert::toBoolean($data['IsGiftCard']);
                    if (isset($data['Money']))          $this->Money        = \ClubSpeed\Utility\Convert::toNumber ($data['Money']);
                    if (isset($data['Points']))         $this->Points       = \ClubSpeed\Utility\Convert::toNumber ($data['Points']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}