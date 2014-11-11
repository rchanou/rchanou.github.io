<?php

namespace ClubSpeed\Database\Records;

class CheckTotals_V extends BaseRecord {

    public static $table      = 'dbo.CheckTotals_V';
    public static $tableAlias = 'ct_v';
    public static $key        = 'CheckID';
    
    public $CheckID; // Check Fields
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
    public $CheckSubtotal;
    public $CheckTax;
    public $CheckTotal;
    public $CheckPaidTax;
    public $CheckPaidTotal;
    public $CheckRemainingTax;
    public $CheckRemainingTotal;
    public $CheckDetailID; // Check Detail fields
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
    public $CheckDetailSubtotal;
    public $CheckDetailTax;
    public $CheckDetailTotal;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['CheckID']))                    $this->CheckID                  = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckID']);
                    if (isset($data['CustID']))                     $this->CustID                   = \ClubSpeed\Utility\Convert::toNumber          ($data['CustID']);
                    if (isset($data['CheckType']))                  $this->CheckType                = \ClubSpeed\Utility\Convert::toString          ($data['CheckType']);
                    if (isset($data['CheckStatus']))                $this->CheckStatus              = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckStatus']);
                    if (isset($data['CheckName']))                  $this->CheckName                = \ClubSpeed\Utility\Convert::toString          ($data['CheckName']);
                    if (isset($data['UserID']))                     $this->UserID                   = \ClubSpeed\Utility\Convert::toNumber          ($data['UserID']);
                    if (isset($data['CheckTotalApplied']))          $this->CheckTotalApplied        = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckTotalApplied']);
                    if (isset($data['CheckSubtotal']))              $this->CheckSubtotal            = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckSubtotal']);
                    if (isset($data['CheckTax']))                   $this->CheckTax                 = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckTax']);
                    if (isset($data['CheckTotal']))                 $this->CheckTotal               = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckTotal']);
                    if (isset($data['CheckPaidTax']))               $this->CheckPaidTax             = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckPaidTax']);
                    if (isset($data['CheckPaidTotal']))             $this->CheckPaidTotal           = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckPaidTotal']);
                    if (isset($data['CheckRemainingTax']))          $this->CheckRemainingTax        = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckRemainingTax']);
                    if (isset($data['CheckRemainingTotal']))        $this->CheckRemainingTotal      = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckRemainingTotal']);
                    if (isset($data['BrokerName']))                 $this->BrokerName               = \ClubSpeed\Utility\Convert::toString          ($data['BrokerName']);
                    if (isset($data['Notes']))                      $this->Notes                    = \ClubSpeed\Utility\Convert::toString          ($data['Notes']);
                    if (isset($data['Gratuity']))                   $this->Gratuity                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Gratuity']);
                    if (isset($data['Fee']))                        $this->Fee                      = \ClubSpeed\Utility\Convert::toNumber          ($data['Fee']);
                    if (isset($data['OpenedDate']))                 $this->OpenedDate               = \ClubSpeed\Utility\Convert::toDateForServer   ($data['OpenedDate']);
                    if (isset($data['ClosedDate']))                 $this->ClosedDate               = \ClubSpeed\Utility\Convert::toDateForServer   ($data['ClosedDate']);
                    if (isset($data['IsTaxExempt']))                $this->IsTaxExempt              = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsTaxExempt']);
                    if (isset($data['Discount']))                   $this->Discount                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Discount']);
                    if (isset($data['CheckDetailID']))              $this->CheckDetailID            = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailID']);
                    if (isset($data['CheckDetailStatus']))          $this->CheckDetailStatus        = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailStatus']);
                    if (isset($data['CheckDetailType']))            $this->CheckDetailType          = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailType']);
                    if (isset($data['ProductID']))                  $this->ProductID                = \ClubSpeed\Utility\Convert::toNumber          ($data['ProductID']);
                    if (isset($data['ProductName']))                $this->ProductName              = \ClubSpeed\Utility\Convert::toString          ($data['ProductName']);
                    if (isset($data['CreatedDate']))                $this->CreatedDate              = \ClubSpeed\Utility\Convert::toDateForServer   ($data['CreatedDate']);
                    if (isset($data['Qty']))                        $this->Qty                      = \ClubSpeed\Utility\Convert::toNumber          ($data['Qty']);
                    if (isset($data['UnitPrice']))                  $this->UnitPrice                = \ClubSpeed\Utility\Convert::toNumber          ($data['UnitPrice']);
                    if (isset($data['UnitPrice2']))                 $this->UnitPrice2               = \ClubSpeed\Utility\Convert::toNumber          ($data['UnitPrice2']);
                    if (isset($data['DiscountApplied']))            $this->DiscountApplied          = \ClubSpeed\Utility\Convert::toNumber          ($data['DiscountApplied']);
                    if (isset($data['TaxID']))                      $this->TaxID                    = \ClubSpeed\Utility\Convert::toNumber          ($data['TaxID']);
                    if (isset($data['TaxPercent']))                 $this->TaxPercent               = \ClubSpeed\Utility\Convert::toNumber          ($data['TaxPercent']);
                    if (isset($data['VoidNotes']))                  $this->VoidNotes                = \ClubSpeed\Utility\Convert::toString          ($data['VoidNotes']);
                    if (isset($data['CID']))                        $this->CID                      = \ClubSpeed\Utility\Convert::toString          ($data['CID']);
                    if (isset($data['VID']))                        $this->VID                      = \ClubSpeed\Utility\Convert::toString          ($data['VID']);
                    if (isset($data['BonusValue']))                 $this->BonusValue               = \ClubSpeed\Utility\Convert::toNumber          ($data['BonusValue']);
                    if (isset($data['PaidValue']))                  $this->PaidValue                = \ClubSpeed\Utility\Convert::toNumber          ($data['PaidValue']);
                    if (isset($data['ComValue']))                   $this->ComValue                 = \ClubSpeed\Utility\Convert::toNumber          ($data['ComValue']);
                    if (isset($data['Entitle1']))                   $this->Entitle1                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle1']);
                    if (isset($data['Entitle2']))                   $this->Entitle2                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle2']);
                    if (isset($data['Entitle3']))                   $this->Entitle3                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle3']);
                    if (isset($data['Entitle4']))                   $this->Entitle4                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle4']);
                    if (isset($data['Entitle5']))                   $this->Entitle5                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle5']);
                    if (isset($data['Entitle6']))                   $this->Entitle6                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle6']);
                    if (isset($data['Entitle7']))                   $this->Entitle7                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle7']);
                    if (isset($data['Entitle8']))                   $this->Entitle8                 = \ClubSpeed\Utility\Convert::toNumber          ($data['Entitle8']);
                    if (isset($data['M_Points']))                   $this->M_Points                 = \ClubSpeed\Utility\Convert::toNumber          ($data['M_Points']);
                    if (isset($data['M_CustID']))                   $this->M_CustID                 = \ClubSpeed\Utility\Convert::toNumber          ($data['M_CustID']);
                    if (isset($data['M_OldMembershiptypeID']))      $this->M_OldMembershiptypeID    = \ClubSpeed\Utility\Convert::toNumber          ($data['M_OldMembershiptypeID']);
                    if (isset($data['M_NewMembershiptypeID']))      $this->M_NewMembershiptypeID    = \ClubSpeed\Utility\Convert::toNumber          ($data['M_NewMembershiptypeID']);
                    if (isset($data['M_Days']))                     $this->M_Days                   = \ClubSpeed\Utility\Convert::toNumber          ($data['M_Days']);
                    if (isset($data['M_PrimaryMembership']))        $this->M_PrimaryMembership      = \ClubSpeed\Utility\Convert::toBoolean         ($data['M_PrimaryMembership']);
                    if (isset($data['P_PointTypeID']))              $this->P_PointTypeID            = \ClubSpeed\Utility\Convert::toNumber          ($data['P_PointTypeID']);
                    if (isset($data['P_Points']))                   $this->P_Points                 = \ClubSpeed\Utility\Convert::toNumber          ($data['P_Points']);
                    if (isset($data['P_CustID']))                   $this->P_CustID                 = \ClubSpeed\Utility\Convert::toNumber          ($data['P_CustID']);
                    if (isset($data['R_Points']))                   $this->R_Points                 = \ClubSpeed\Utility\Convert::toNumber          ($data['R_Points']);
                    if (isset($data['DiscountUserID']))             $this->DiscountUserID           = \ClubSpeed\Utility\Convert::toNumber          ($data['DiscountUserID']);
                    if (isset($data['DiscountDesc']))               $this->DiscountDesc             = \ClubSpeed\Utility\Convert::toString          ($data['DiscountDesc']);
                    if (isset($data['CalculateType']))              $this->CalculateType            = \ClubSpeed\Utility\Convert::toNumber          ($data['CalculateType']);
                    if (isset($data['DiscountID']))                 $this->DiscountID               = \ClubSpeed\Utility\Convert::toNumber          ($data['DiscountID']);
                    if (isset($data['DiscountNotes']))              $this->DiscountNotes            = \ClubSpeed\Utility\Convert::toString          ($data['DiscountNotes']);
                    if (isset($data['G_Points']))                   $this->G_Points                 = \ClubSpeed\Utility\Convert::toNumber          ($data['G_Points']);
                    if (isset($data['G_CustID']))                   $this->G_CustID                 = \ClubSpeed\Utility\Convert::toNumber          ($data['G_CustID']);
                    if (isset($data['GST']))                        $this->GST                      = \ClubSpeed\Utility\Convert::toNumber          ($data['GST']);
                    if (isset($data['M_DaysAdded']))                $this->M_DaysAdded              = \ClubSpeed\Utility\Convert::toNumber          ($data['M_DaysAdded']);
                    if (isset($data['S_SaleBy']))                   $this->S_SaleBy                 = \ClubSpeed\Utility\Convert::toNumber          ($data['S_SaleBy']);
                    if (isset($data['S_NoOfLapsOrSeconds']))        $this->S_NoOfLapsOrSeconds      = \ClubSpeed\Utility\Convert::toNumber          ($data['S_NoOfLapsOrSeconds']);
                    if (isset($data['S_CustID']))                   $this->S_CustID                 = \ClubSpeed\Utility\Convert::toNumber          ($data['S_CustID']);
                    if (isset($data['S_Vol']))                      $this->S_Vol                    = \ClubSpeed\Utility\Convert::toNumber          ($data['S_Vol']);
                    if (isset($data['CadetQty']))                   $this->CadetQty                 = \ClubSpeed\Utility\Convert::toNumber          ($data['CadetQty']);
                    if (isset($data['CheckDetailSubtotal']))        $this->CheckDetailSubtotal      = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailSubtotal']);
                    if (isset($data['CheckDetailTax']))             $this->CheckDetailTax           = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailTax']);
                    if (isset($data['CheckDetailTotal']))           $this->CheckDetailTotal         = \ClubSpeed\Utility\Convert::toNumber          ($data['CheckDetailTotal']);
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
            if (is_null($this->UserID) || !is_int($this->UserID))
                throw new \RequiredArgumentMissingException("Check create requires UserID to be an integer! Received: " . $this->UserID);
            if (is_null($this->CustID) || !is_int($this->CustID))
                throw new \RequiredArgumentMissingException("Check create requires CustID to be an integer! Received: " . $this->CustID);
                break;
            case 'update':
                // todo
                break;
        }
    }
}