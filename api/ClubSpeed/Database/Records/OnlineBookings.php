<?php

namespace ClubSpeed\Database\Records;

class OnlineBookings extends BaseRecord {

    public static $table      = 'dbo.OnlineBookings';
    public static $tableAlias = 'ob';
    public static $key        = 'OnlineBookingsID';
    
    public $OnlineBookingsID;
    public $HeatMainID;
    public $ProductsID;
    public $IsPublic;
    public $QuantityTotal;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['OnlineBookingsID']))   $this->OnlineBookingsID = \ClubSpeed\Utility\Convert::toNumber($data['OnlineBookingsID']);
                    if (isset($data['HeatMainID']))         $this->HeatMainID       = \ClubSpeed\Utility\Convert::toNumber($data['HeatMainID']);
                    if (isset($data['ProductsID']))         $this->ProductsID       = \ClubSpeed\Utility\Convert::toNumber($data['ProductsID']);
                    if (isset($data['QuantityTotal']))      $this->QuantityTotal    = \ClubSpeed\Utility\Convert::toNumber($data['QuantityTotal']);
                    if (isset($data['IsPublic']))           $this->IsPublic         = \ClubSpeed\Utility\Convert::toBoolean($data['IsPublic']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}