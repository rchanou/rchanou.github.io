<?php

namespace ClubSpeed\Database\Records;

class CheckTotals_V extends BaseRecord {
    protected static $_definition;
    
    // check fields
    public $CheckID;
    public $CustID;
    public $CheckType;
    public $CheckStatus;
    public $CheckName;
    public $UserID;
    public $CheckTotalApplied;
    public $BrokerName;
    public $Notes;
    public $Gratuity;
    public $Fee;
    public $OpenedDate;
    public $ClosedDate;
    public $IsTaxExempt;
    public $Discount;
    public $CheckDiscountID;
    public $CheckDiscountNotes;
    public $CheckDiscountUserID;
    public $CheckSubtotal;
    public $CheckTax;
    public $CheckGST;
    public $CheckPST;
    public $CheckTotal;
    public $CheckPaidTax;
    public $CheckPaidTotal;
    public $CheckRemainingTax;
    public $CheckRemainingTotal;

    // check detail fields
    public $CheckDetailID;
    public $CheckDetailStatus;
    public $CheckDetailType;
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
    public $CheckDetailDiscountUserID;
    public $CheckDetailDiscountDesc;
    public $CheckDetailDiscountCalculateType;
    public $CheckDetailDiscountID;
    public $CheckDetailDiscountNotes;
    public $G_Points;
    public $G_CustID;
    public $GST;
    public $M_DaysAdded;
    public $S_SaleBy;
    public $S_NoOfLapsOrSeconds;
    public $S_CustID;
    public $S_Vol;
    public $CadetQty;
    public $CheckDetailSubtotal;
    public $CheckDetailTax;
    public $CheckDetailGST;
    public $CheckDetailPST;
    public $CheckDetailTotal;
}