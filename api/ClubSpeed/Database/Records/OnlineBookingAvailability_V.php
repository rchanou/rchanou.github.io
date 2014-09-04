<?php

namespace ClubSpeed\Database\Records;

require_once(__DIR__.'/DbRecord.php');

class OnlineBookingAvailability_V extends DbRecord {

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
    public $ProductDescription;
    public $ProductsID;
    public $ProductSpotsAvailableOnline;
    public $ProductSpotsTotal;
    public $ProductSpotsUsed;

    public function __construct($data = array()) {
        $this->load($data);

        // if (isset($data)) {
        //     if (is_array($data)) {
        //         if (!empty($data)) {
        //             // move this to inline on the __construct call?
        //             $this->HeatDescription              = \ClubSpeed\Utility\Convert::toString ($data['HeatDescription']);
        //             $this->HeatEndsAt                   = \ClubSpeed\Utility\Convert::toString ($data['HeatEndsAt']);
        //             $this->HeatNo                       = \ClubSpeed\Utility\Convert::toNumber ($data['HeatNo']);
        //             $this->HeatSpotsAvailableCombined   = \ClubSpeed\Utility\Convert::toNumber ($data['HeatSpotsAvailableCombined']);
        //             $this->HeatSpotsAvailableOnline     = \ClubSpeed\Utility\Convert::toNumber ($data['HeatSpotsAvailableOnline']);
        //             $this->HeatSpotsTotalActual         = \ClubSpeed\Utility\Convert::toNumber ($data['HeatSpotsTotalActual']);
        //             $this->HeatStartsAt                 = \ClubSpeed\Utility\Convert::toString ($data['HeatStartsAt']);
        //             $this->HeatTypeNo                   = \ClubSpeed\Utility\Convert::toNumber ($data['HeatTypeNo']);
        //             $this->IsPublic                     = \ClubSpeed\Utility\Convert::toBoolean($data['IsPublic']);
        //             $this->OnlineBookingsID             = \ClubSpeed\Utility\Convert::toNumber ($data['OnlineBookingsID']);
        //             $this->Price1                       = \ClubSpeed\Utility\Convert::toNumber ($data['Price1']);
        //             $this->ProductDescription           = \ClubSpeed\Utility\Convert::toString ($data['ProductDescription']);
        //             $this->ProductsID                   = \ClubSpeed\Utility\Convert::toNumber ($data['ProductsID']);
        //             $this->ProductSpotsAvailableOnline  = \ClubSpeed\Utility\Convert::toNumber ($data['ProductSpotsAvailableOnline']);
        //             $this->ProductSpotsTotal            = \ClubSpeed\Utility\Convert::toNumber ($data['ProductSpotsTotal']);
        //             $this->ProductSpotsUsed             = \ClubSpeed\Utility\Convert::toNumber ($data['ProductSpotsUsed']);
        //         }
        //     }
        //     else {
        //         $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
        //     }
        //     // $this->HeatDescription              = isset($data['HeatDescription'])               ? $data['HeatDescription']              : @$data['heatDescription'];
        //     // $this->HeatEndsAt                   = isset($data['HeatEndsAt'])                    ? $data['HeatEndsAt']                   : @$data['heatEndsAt'];
        //     // $this->HeatNo                       = isset($data['HeatNo'])                        ? $data['HeatNo']                       : @$data['heatNo'];
        //     // $this->HeatSpotsAvailableCombined   = isset($data['HeatSpotsAvailableCombined'])    ? $data['HeatSpotsAvailableCombined']   : @$data['heatSpotsTotalCombined'];
        //     // $this->HeatSpotsAvailableOnline     = isset($data['HeatSpotsAvailableOnline'])      ? $data['HeatSpotsAvailableOnline']     : @$data['heatSpotsAvailableOnline'];
        //     // $this->HeatSpotsTotalActual         = isset($data['HeatSpotsTotalActual'])          ? $data['HeatSpotsTotalActual']         : @$data['heatSpotsTotalActual'];
        //     // $this->HeatStartsAt                 = isset($data['HeatStartsAt'])                  ? $data['HeatStartsAt']                 : @$data['heatStartsAt'];
        //     // $this->HeatTypeNo                   = isset($data['HeatTypeNo'])                    ? $data['HeatTypeNo']                   : @$data['heatTypeNo'];
        //     // $this->IsPublic                     = isset($data['IsPublic'])                      ? $data['IsPublic']                     : @$data['isPublic'];
        //     // $this->OnlineBookingsID             = isset($data['OnlineBookingsID'])              ? $data['OnlineBookingsID']             : @$data['onlineBookingsId'];
        //     // $this->Price1                       = isset($data['Price1'])                        ? $data['Price1']                       : @$data['price1'];
        //     // $this->ProductDescription           = isset($data['ProductDescription'])            ? $data['ProductDescription']           : @$data['productDescription'];
        //     // $this->ProductsID                   = isset($data['ProductsID'])                    ? $data['ProductsID']                   : @$data['productsId'];
        //     // $this->ProductSpotsAvailableOnline  = isset($data['ProductSpotsAvailableOnline'])   ? $data['ProductSpotsAvailableOnline']  : @$data['productSpotsAvailableOnline'];
        //     // $this->ProductSpotsTotal            = isset($data['ProductSpotsTotal'])             ? $data['ProductSpotsTotal']            : @$data['productSpotsTotal'];
        //     // $this->ProductSpotsUsed             = isset($data['ProductSpotsUsed'])              ? $data['ProductSpotsUsed']             : @$data['productSpotsUsed'];
            
        // }
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    // move this to inline on the __construct call?
                    if (isset($data['HeatDescription']))                $this->HeatDescription              = \ClubSpeed\Utility\Convert::toString ($data['HeatDescription']);
                    if (isset($data['HeatEndsAt']))                     $this->HeatEndsAt                   = \ClubSpeed\Utility\Convert::toString ($data['HeatEndsAt']);
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
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    protected function _convert() {
        // // move this to inline on the __construct call?
        // $this->HeatDescription              = \ClubSpeed\Utility\Convert::toString ($this->HeatDescription);
        // $this->HeatEndsAt                   = \ClubSpeed\Utility\Convert::toString ($this->HeatEndsAt);
        // $this->HeatNo                       = \ClubSpeed\Utility\Convert::toNumber ($this->HeatNo);
        // $this->HeatSpotsAvailableCombined   = \ClubSpeed\Utility\Convert::toNumber ($this->HeatSpotsAvailableCombined);
        // $this->HeatSpotsAvailableOnline     = \ClubSpeed\Utility\Convert::toNumber ($this->HeatSpotsAvailableOnline);
        // $this->HeatSpotsTotalActual         = \ClubSpeed\Utility\Convert::toNumber ($this->HeatSpotsTotalActual);
        // $this->HeatStartsAt                 = \ClubSpeed\Utility\Convert::toString ($this->HeatStartsAt);
        // $this->HeatTypeNo                   = \ClubSpeed\Utility\Convert::toNumber ($this->HeatTypeNo);
        // $this->IsPublic                     = \ClubSpeed\Utility\Convert::toBoolean($this->IsPublic);
        // $this->OnlineBookingsID             = \ClubSpeed\Utility\Convert::toNumber ($this->OnlineBookingsID);
        // $this->Price1                       = \ClubSpeed\Utility\Convert::toNumber ($this->Price1);
        // $this->ProductDescription           = \ClubSpeed\Utility\Convert::toString ($this->ProductDescription);
        // $this->ProductsID                   = \ClubSpeed\Utility\Convert::toNumber ($this->ProductsID);
        // $this->ProductSpotsAvailableOnline  = \ClubSpeed\Utility\Convert::toNumber ($this->ProductSpotsAvailableOnline);
        // $this->ProductSpotsTotal            = \ClubSpeed\Utility\Convert::toNumber ($this->ProductSpotsTotal);
        // $this->ProductSpotsUsed             = \ClubSpeed\Utility\Convert::toNumber ($this->ProductSpotsUsed);
    }

    public function create() {
        throw new \InvalidDbOperationException("OnlineBookingAvailability_V cannot be inserted into the database!");
    }

    public function update() {
        throw new \InvalidDbOperationException("OnlineBookingAvailability_V cannot be updated in the database!");
    }

    public function delete() {
        throw new \InvalidDbOperationException("OnlineBookingAvailability_V cannot be deleted from the database!");
    }
}