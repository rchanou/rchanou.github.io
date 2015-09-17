<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums;

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
    }

    public final function create($params = array()) {
        $logic = &$this->logic; // we have to do this for PHP 5.3, as we run into issues with protected/private -- 5.4 has access to correctly scoped $this
        return parent::_create($params, function($checkDetails) use (&$logic) {
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
            $checkDetails->TaxID = $tax->TaxID;
            $checkDetails->TaxPercent = $tax->Amount;
            $checkDetails->Status = Enums::CHECK_DETAIL_STATUS_IS_NEW;
            $checkDetails->CreatedDate = \ClubSpeed\Utility\Convert::getDate();
            $checkDetails->ProductName = $product->Description;
            $checkDetails->UnitPrice = $product->Price1;
            $checkDetails->UnitPrice2 = $product->Price2;
            $checkDetails->Type = $product->ProductType; // seems to be the product type -- DOUBLE CHECK
            $checkDetails->GST = $tax->GST;
            $checkDetails->P_Points = ($product->P_Points ?: 0) * $checkDetails->Qty; // use 0 instead of null (for the front end)
            if (!is_null($checkDetails->P_Points) && $checkDetails->P_Points > 0)
                $checkDetails->P_CustID = $check->CustID;
            $checkDetails->R_Points = $product->R_Points; // we want nulls to stay null, don't convert to 0
            $checkDetails->G_Points = $product->G_Points; // leave G_Points as null as well
            return $checkDetails;
        });
    }
}
