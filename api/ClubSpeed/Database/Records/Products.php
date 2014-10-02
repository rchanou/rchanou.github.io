<?php

namespace ClubSpeed\Database\Records;

class Products extends BaseRecord {

    public static $table      = 'dbo.Products';
    public static $tableAlias = 'prdcts';
    public static $key        = 'ProductID';
    
    public $ProductID;
    public $ProductType;
    public $Description;
    public $Price1;
    public $Price2;
    public $Price3;
    public $Price4;
    public $Price5;
    public $TaxID;
    public $ProductClassID;
    public $LargeIcon;
    public $IsSpecial;
    public $AvailableDay;
    public $AvailableFromTime;
    public $AvailableToTime;
    public $IsRequiredMembership;
    public $ShowOnWeb;
    public $IsTrackable;
    public $IsShowStat;
    public $IsInventory;
    public $Cost;
    public $Req;
    public $VendorID;
    public $Enabled;
    public $Deleted;
    public $P_PointTypeID;
    public $P_Points;
    public $BonusValue;
    public $PaidValue;
    public $ComValue;
    public $Entitle1;
    public $Entitle2;
    public $Entitle3;
    public $Entitle4;
    public $Entitle5;
    public $Entitle6;
    public $Entitle7;
    public $Entitle8;
    public $M_Points;
    public $M_MembershiptypeID;
    public $R_Points;
    public $R_LocalOnly;
    public $G_Points;
    public $S_SaleBy;
    public $S_NoOfLapsOrSeconds;
    public $S_CustID;
    public $S_Vol;
    public $PriceCadet;
    public $DateCreated;
    public $WebShop;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['ProductID']))              $this->ProductID            = \ClubSpeed\Utility\Convert::toNumber          ($data['ProductID']);
                    if (isset($data['ProductType']))            $this->ProductType          = \ClubSpeed\Utility\Convert::toNumber          ($data['ProductType']);
                    if (isset($data['Description']))            $this->Description          = \ClubSpeed\Utility\Convert::toString          ($data['Description']);
                    if (isset($data['Price1']))                 $this->Price1               = \ClubSpeed\Utility\Convert::toNumber          ($data['Price1']);
                    if (isset($data['Price2']))                 $this->Price2               = \ClubSpeed\Utility\Convert::toNumber          ($data['Price2']);
                    if (isset($data['Price3']))                 $this->Price3               = \ClubSpeed\Utility\Convert::toNumber          ($data['Price3']);
                    if (isset($data['Price4']))                 $this->Price4               = \ClubSpeed\Utility\Convert::toNumber          ($data['Price4']);
                    if (isset($data['Price5']))                 $this->Price5               = \ClubSpeed\Utility\Convert::toNumber          ($data['Price5']);
                    if (isset($data['TaxID']))                  $this->TaxID                = \ClubSpeed\Utility\Convert::toNumber          ($data['TaxID']);
                    if (isset($data['ProductClassID']))         $this->ProductClassID       = \ClubSpeed\Utility\Convert::toNumber          ($data['ProductClassID']);
                    if (isset($data['LargeIcon']))              $this->LargeIcon            = \ClubSpeed\Utility\Convert::toString          ($data['LargeIcon']);
                    if (isset($data['IsSpecial']))              $this->IsSpecial            = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsSpecial']);
                    if (isset($data['AvailableDay']))           $this->AvailableDay         = \ClubSpeed\Utility\Convert::toString          ($data['AvailableDay']);
                    if (isset($data['AvailableFromTime']))      $this->AvailableFromTime    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['AvailableFromTime']);
                    if (isset($data['AvailableToTime']))        $this->AvailableToTime      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['AvailableToTime']);
                    if (isset($data['IsRequiredMembership']))   $this->IsRequiredMembership = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsRequiredMembership']);
                    if (isset($data['ShowOnWeb']))              $this->ShowOnWeb            = \ClubSpeed\Utility\Convert::toBoolean         ($data['ShowOnWeb']);
                    if (isset($data['IsTrackable']))            $this->IsTrackable          = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsTrackable']);
                    if (isset($data['IsShowStat']))             $this->IsShowStat           = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsShowStat']);
                    if (isset($data['IsInventory']))            $this->IsInventory          = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsInventory']);
                    if (isset($data['Cost']))                   $this->Cost                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Cost']);
                    if (isset($data['Req']))                    $this->Req                  = \ClubSpeed\Utility\Convert::toNumber          ($data['Req']);
                    if (isset($data['VendorID']))               $this->VendorID             = \ClubSpeed\Utility\Convert::toNumber          ($data['VendorID']);
                    if (isset($data['Enabled']))                $this->Enabled              = \ClubSpeed\Utility\Convert::toBoolean         ($data['Enabled']);
                    if (isset($data['Deleted']))                $this->Deleted              = \ClubSpeed\Utility\Convert::toBoolean         ($data['Deleted']);
                    if (isset($data['P_PointTypeID']))          $this->P_PointTypeID        = \ClubSpeed\Utility\Convert::toNumber          ($data['P_PointTypeID']);
                    if (isset($data['P_Points']))               $this->P_Points             = \ClubSpeed\Utility\Convert::toNumber          ($data['P_Points']);
                    if (isset($data['BonusValue']))             $this->BonusValue           = \ClubSpeed\Utility\Convert::toNumber          ($data['BonusValue']);
                    if (isset($data['PaidValue']))              $this->PaidValue            = \ClubSpeed\Utility\Convert::toNumber          ($data['PaidValue']);
                    if (isset($data['ComValue']))               $this->ComValue             = \ClubSpeed\Utility\Convert::toNumber          ($data['ComValue']);
                    if (isset($data['Entitle1']))               $this->Entitle1             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle1']);
                    if (isset($data['Entitle2']))               $this->Entitle2             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle2']);
                    if (isset($data['Entitle3']))               $this->Entitle3             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle3']);
                    if (isset($data['Entitle4']))               $this->Entitle4             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle4']);
                    if (isset($data['Entitle5']))               $this->Entitle5             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle5']);
                    if (isset($data['Entitle6']))               $this->Entitle6             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle6']);
                    if (isset($data['Entitle7']))               $this->Entitle7             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle7']);
                    if (isset($data['Entitle8']))               $this->Entitle8             = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle8']);
                    if (isset($data['M_Points']))               $this->M_Points             = \ClubSpeed\Utility\Convert::toNumber          ($data['M_Points']);
                    if (isset($data['M_MembershiptypeID']))     $this->M_MembershiptypeID   = \ClubSpeed\Utility\Convert::toNumber          ($data['M_MembershiptypeID']);
                    if (isset($data['R_Points']))               $this->R_Points             = \ClubSpeed\Utility\Convert::toNumber          ($data['R_Points']);
                    if (isset($data['R_LocalOnly']))            $this->R_LocalOnly          = \ClubSpeed\Utility\Convert::toBoolean         ($data['R_LocalOnly']);
                    if (isset($data['G_Points']))               $this->G_Points             = \ClubSpeed\Utility\Convert::toNumber          ($data['G_Points']);
                    if (isset($data['S_SaleBy']))               $this->S_SaleBy             = \ClubSpeed\Utility\Convert::toNumber          ($data['S_SaleBy']);
                    if (isset($data['S_NoOfLapsOrSeconds']))    $this->S_NoOfLapsOrSeconds  = \ClubSpeed\Utility\Convert::toNumber          ($data['S_NoOfLapsOrSeconds']);
                    if (isset($data['S_CustID']))               $this->S_CustID             = \ClubSpeed\Utility\Convert::toNumber          ($data['S_CustID']);
                    if (isset($data['S_Vol']))                  $this->S_Vol                = \ClubSpeed\Utility\Convert::toNumber          ($data['S_Vol']);
                    if (isset($data['PriceCadet']))             $this->PriceCadet           = \ClubSpeed\Utility\Convert::toNumber          ($data['PriceCadet']);
                    if (isset($data['DateCreated']))            $this->DateCreated          = \ClubSpeed\Utility\Convert::toDateForServer   ($data['DateCreated']);
                    if (isset($data['WebShop']))                $this->WebShop              = \ClubSpeed\Utility\Convert::toBoolean         ($data['WebShop']);
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
                // todo
                break;
            case 'update':
                // todo
                break;
        }
    }
}