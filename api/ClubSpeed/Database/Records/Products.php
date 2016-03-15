<?php

namespace ClubSpeed\Database\Records;

class Products extends BaseRecord {
    protected static $_definition;
    
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
    // public $LargeIcon; // take this out - its just a huge performance sink for data we don't use
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
}
