<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed checks.
 */
class CheckDetailsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the CheckDetailsLogic class.
     *
     * The CheckDetailsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->checkDetails;

        $this->insertable = array(
            // 'CheckDetailID'
              'CheckID'
            // , 'Status'
            , 'Type'
            , 'ProductID'
            // , 'ProductName'
            // , 'CreatedDate'
            , 'Qty'
            // , 'UnitPrice'
            // , 'UnitPrice2'
            , 'DiscountApplied'
            // , 'TaxID'
            // , 'TaxPercent'
            , 'VoidNotes'
            , 'CID'
            , 'VID'
            , 'BonusValue'
            , 'PaidValue'
            , 'ComValue'
            , 'Entitle1'
            , 'Entitle2'
            , 'Entitle3'
            , 'Entitle4'
            , 'Entitle5'
            , 'Entitle6'
            , 'Entitle7'
            , 'Entitle8'
            , 'M_Points'
            , 'M_CustID'
            , 'M_OldMembershiptypeID'
            , 'M_NewMembershiptypeID'
            , 'M_Days'
            , 'M_PrimaryMembership'
            , 'P_PointTypeID'
            , 'P_Points'
            , 'P_CustID'
            , 'R_Points'
            , 'DiscountUserID'
            , 'DiscountDesc'
            , 'CalculateType'
            , 'DiscountID'
            , 'DiscountNotes'
            , 'G_Points'
            , 'G_CustID'
            , 'GST'
            , 'M_DaysAdded'
            , 'S_SaleBy'
            , 'S_NoOfLapsOrSeconds'
            , 'S_CustID'
            , 'S_Vol'
            , 'CadetQty'
        );

        $this->updatable = array(
              'CadetQty'
            , 'Qty'
            , 'Status'
            , 'Type'
        );
    }

    public final function create($params = array()) {
        $logic = &$this->logic; // we have to do this for PHP 5.3, as we run into issues with protected/private -- 5.4 has access to correctly scoped $this
        return parent::_create($params, function($checkDetails) use (&$logic) {
            $checkDetails->validate('insert'); // validate the physical structure early, before trying to get foreign keys
            if ((is_null($checkDetails->Qty) || !is_int($checkDetails->Qty) || $checkDetails->Qty < 1) && (is_null($checkDetails->CadetQty) || !is_int($checkDetails->CadetQty) || $checkDetails->CadetQty < 1))
                throw new \RequiredArgumentMissingException("CheckDetails create requires a positive Qty or CadetQty! Received Qty: " . $checkDetails->Qty . " and CadetQty: " . $checkDetails->CadetQty);
            $check = $logic->checks->get($checkDetails->CheckID);
            $check = $check[0];
            $product = $logic->products->get($checkDetails->ProductID);
            $product = $product[0];
            $tax = $logic->taxes->get($product->TaxID);
            $tax = $tax[0];
            if (isset($checkDetails->Qty) && $checkDetails->Qty < 0) // disallow negative quantities
                $checkDetails->Qty = 0;
            if (isset($checkDetails->CadetQty) && $checkDetails->CadetQty < 0) // disallow negative quantities
                $checkDetails->CadetQty = 0;
            if (is_null($checkDetails->Type))
                $checkDetails->Type = 1;
            $checkDetails->TaxPercent = $tax->Amount;
            $checkDetails->Status = 1; // Enum for IsNew
            $checkDetails->CreatedDate = \ClubSpeed\Utility\Convert::getDate();
            $checkDetails->ProductName = $product->Description;
            $checkDetails->UnitPrice = $product->Price1;
            $checkDetails->UnitPrice2 = $product->Price2;
            $checkDetails->GST = $tax->GST;
            $checkDetails->P_Points = ($product->P_Points ?: 0) * $checkDetails->Qty;
            if (!empty($checkDetails->P_Points)) {
                // apply the check's CustID to checkDetails.P_CustID
                $checkDetails->P_CustID = $check->CustID;

                // add PointHistory items here, or wait until the check is paid for?
            }
            return $checkDetails;
        });
    }
}