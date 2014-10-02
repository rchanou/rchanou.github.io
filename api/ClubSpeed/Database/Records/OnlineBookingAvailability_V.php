<?php

namespace ClubSpeed\Database\Records;

class OnlineBookingAvailability_V extends BaseRecord {

    public static $table      = 'dbo.OnlineBookingAvailability_V';
    public static $tableAlias = 'obav';
    public static $key        = 'OnlineBookingsID';

    public $HeatDescription;
    public $HeatEndsAt;
    public $HeatNo;
    public $HeatSpotsAvailableCombined;
    public $HeatSpotsAvailableOnline;
    public $HeatSpotsTotalActual;
    public $HeatStartsAt;
    public $HeatTypeNo;
    public $IsPublic;
    public $OnlineBookingsID;
    public $Price1;
    public $ProductType;
    public $ProductDescription;
    public $ProductsID;
    public $ProductSpotsAvailableOnline;
    public $ProductSpotsTotal;
    public $ProductSpotsUsed;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['HeatDescription']))                $this->HeatDescription              = \ClubSpeed\Utility\Convert::toString ($data['HeatDescription']);
                    if (isset($data['HeatNo']))                         $this->HeatNo                       = \ClubSpeed\Utility\Convert::toNumber ($data['HeatNo']);
                    if (isset($data['HeatSpotsAvailableCombined']))     $this->HeatSpotsAvailableCombined   = \ClubSpeed\Utility\Convert::toNumber ($data['HeatSpotsAvailableCombined']);
                    if (isset($data['HeatSpotsAvailableOnline']))       $this->HeatSpotsAvailableOnline     = \ClubSpeed\Utility\Convert::toNumber ($data['HeatSpotsAvailableOnline']);
                    if (isset($data['HeatSpotsTotalActual']))           $this->HeatSpotsTotalActual         = \ClubSpeed\Utility\Convert::toNumber ($data['HeatSpotsTotalActual']);
                    if (isset($data['HeatStartsAt']))                   $this->HeatStartsAt                 = \ClubSpeed\Utility\Convert::toString ($data['HeatStartsAt']);
                    if (isset($data['HeatTypeNo']))                     $this->HeatTypeNo                   = \ClubSpeed\Utility\Convert::toNumber ($data['HeatTypeNo']);
                    if (isset($data['IsPublic']))                       $this->IsPublic                     = \ClubSpeed\Utility\Convert::toBoolean($data['IsPublic']);
                    if (isset($data['OnlineBookingsID']))               $this->OnlineBookingsID             = \ClubSpeed\Utility\Convert::toNumber ($data['OnlineBookingsID']);
                    if (isset($data['Price1']))                         $this->Price1                       = \ClubSpeed\Utility\Convert::toNumber ($data['Price1']);
                    if (isset($data['ProductDescription']))             $this->ProductDescription           = \ClubSpeed\Utility\Convert::toString ($data['ProductDescription']);
                    if (isset($data['ProductsID']))                     $this->ProductsID                   = \ClubSpeed\Utility\Convert::toNumber ($data['ProductsID']);
                    if (isset($data['ProductSpotsAvailableOnline']))    $this->ProductSpotsAvailableOnline  = \ClubSpeed\Utility\Convert::toNumber ($data['ProductSpotsAvailableOnline']);
                    if (isset($data['ProductSpotsTotal']))              $this->ProductSpotsTotal            = \ClubSpeed\Utility\Convert::toNumber ($data['ProductSpotsTotal']);
                    if (isset($data['ProductSpotsUsed']))               $this->ProductSpotsUsed             = \ClubSpeed\Utility\Convert::toNumber ($data['ProductSpotsUsed']);
                    if (isset($data['ProductType']))                    $this->ProductType                  = \ClubSpeed\Utility\Convert::toNumber ($data['ProductType']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}