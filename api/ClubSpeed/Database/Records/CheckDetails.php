<?php

namespace ClubSpeed\Database\Records;

class CheckDetails extends BaseRecord {
    public static $_definition;
    
    public $CheckDetailID;
    public $CheckID;
    public $Status;
    public $Type;
    public $ProductID;
    public $ProductName;
    public $CreatedDate;
    public $Qty;
    public $UnitPrice;
    public $UnitPrice2;
    public $DiscountApplied;
    public $TaxID;
    public $TaxPercent;
    public $VoidNotes;
    public $CID;
    public $VID;
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
    public $M_CustID;
    public $M_OldMembershiptypeID;
    public $M_NewMembershiptypeID;
    public $M_Days;
    public $M_PrimaryMembership;
    public $P_PointTypeID;
    public $P_Points;
    public $P_CustID;
    public $R_Points;
    public $DiscountUserID;
    public $DiscountDesc;
    public $CalculateType;
    public $DiscountID;
    public $DiscountNotes;
    public $G_Points;
    public $G_CustID;
    public $GST;
    public $M_DaysAdded;
    public $S_SaleBy;
    public $S_NoOfLapsOrSeconds;
    public $S_CustID;
    public $S_Vol;
    public $CadetQty;
}