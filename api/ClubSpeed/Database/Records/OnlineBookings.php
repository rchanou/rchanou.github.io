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
                    $this->IsPublic = (
                        isset($data['IsPublic'])
                            ? \ClubSpeed\Utility\Convert::toBoolean($data['IsPublic'])
                            : true
                    );
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    protected function _convert() {
        // $this->OnlineBookingsID = \ClubSpeed\Utility\Convert::toNumber ($this->OnlineBookingsID);
        // $this->HeatMainID       = \ClubSpeed\Utility\Convert::toNumber ($this->HeatMainID);
        // $this->ProductsID       = \ClubSpeed\Utility\Convert::toNumber ($this->ProductsID);
        // $this->IsPublic         = \ClubSpeed\Utility\Convert::toBoolean($this->IsPublic);
        // $this->QuantityTotal    = \ClubSpeed\Utility\Convert::toNumber ($this->QuantityTotal);
    }

    public function toJson() {
        // return array(
        //       'onlineBookingsId'    => $this->OnlineBookingsID
        //     , 'heatMainId'          => $this->HeatMainID
        //     , 'productsId'          => $this->ProductsID
        //     , 'isPublic'            => $this->IsPublic
        //     , 'quantityTotal'       => $this->QuantityTotal
        // );
    }

    public function validate($type) {
        // switch (strtolower($type)) {
        //     case 'insert':
        //         if (!isset($this->HeatMainID))
        //             throw new \InvalidArgumentException("Create online booking requires a HeatMainID!");
        //         if (!isset($this->ProductsID))
        //             throw new \InvalidArgumentException("Create online booking requires a ProductsID!");
        //         if (!isset($this->QuantityTotal) || !is_int($this->QuantityTotal) || $this->QuantityTotal < 1)
        //             throw new \InvalidArgumentException("Create online booking requires a total quantity greater than 0! Received: " . $this->QuantityTotal);
        //         break;
        // }
    }
}