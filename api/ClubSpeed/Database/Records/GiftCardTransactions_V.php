<?php

namespace ClubSpeed\Database\Records;

class GiftCardTransactions_V extends BaseRecord {

    public static $table      = 'dbo.GiftCardTransactions_V';
    public static $tableAlias = 'gch';
    public static $key        = 'HistoryID';

    public $CrdID;
    public $Money;
    public $Points;
    public $Date;
    public $Notes;
    
    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CrdID']))  $this->CrdID    = \ClubSpeed\Utility\Convert::toNumber          ($data['CrdID']);
                    if (isset($data['Money']))  $this->Money    = \ClubSpeed\Utility\Convert::toNumber          ($data['Money']);
                    if (isset($data['Points'])) $this->Points   = \ClubSpeed\Utility\Convert::toNumber          ($data['Points']);
                    if (isset($data['Date']))   $this->Date     = \ClubSpeed\Utility\Convert::toDateForServer   ($data['Date']);
                    if (isset($data['Notes']))  $this->Notes    = \ClubSpeed\Utility\Convert::toString          ($data['Notes']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}