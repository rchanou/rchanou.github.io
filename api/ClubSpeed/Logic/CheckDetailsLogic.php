<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums;
use Clubspeed\Utility\Convert;
use ClubSpeed\Database\Helpers\UnitOfWork;

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

        $self =& $this;
        $befores = array(
            'create' => array($self, 'beforeCreate'),
            'delete' => array($self, 'beforeDelete')
        );
        $this->before('uow', function($uow) use (&$befores)  {
            if (isset($befores[$uow->action]))
                call_user_func($befores[$uow->action], $uow);
        });
    }

    function beforeCreate($uow) {
        $checkDetails =& $uow->data;
        $logic = &$this->logic;
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
        if ($product->ProductType === Enums::PRODUCT_TYPE_GIFT_CARD && $checkDetails->Qty + $checkDetails->CadetQty !== 1)
            throw new \CSException("CheckDetails create with a gift card product must have a quantity of 1! Received Qty: " . $checkDetails->Qty . " and CadetQty: " . $checkDetails->CadetQty);
        $checkDetails->TaxID = $tax->TaxID;
        $checkDetails->TaxPercent = $tax->Amount;
        $checkDetails->Status = Enums::CHECK_DETAIL_STATUS_IS_NEW;
        $checkDetails->CreatedDate = Convert::getDate();
        $checkDetails->ProductName = $product->Description;
        $checkDetails->UnitPrice = $product->Price1;
        $checkDetails->UnitPrice2 = $product->Price2;
        $checkDetails->Type = $product->ProductType; // seems to be the product type -- DOUBLE CHECK
        $checkDetails->GST = $tax->GST;
        $checkDetails->P_Points = $product->P_Points; // null P_Points is reproduceable with a non point product on the front end
        if (!is_null($checkDetails->P_Points) && $checkDetails->P_Points > 0)
            $checkDetails->P_CustID = $check->CustID;
        $checkDetails->R_Points = $product->R_Points; // we want nulls to stay null, don't convert to 0
        $checkDetails->G_Points = $product->G_Points; // leave G_Points as null as well
    }

    function beforeDelete($uow) {
        $checkDetailId = $uow->table_id;
        $checkDetailUow = UnitOfWork::build()->action('get')->table_id($checkDetailId);
        $this->uow($checkDetailUow);
        $checkDetail = $checkDetailUow->data;

        // disallow deletes on check detail status of permanent
        if ($checkDetail->Status === ENUMS::CHECK_DETAIL_STATUS_CANNOT_DELETED) {
            throw new \CSException('Check details with a status of permanent cannot be deleted!');
        }

        // disallow deletes on check detail status of void (matches POS, we think)
        // noting that open check, added item, paid check, voided check, re-opened check,
        // results in a voided item which is not deletable by the POS.
        if ($checkDetail->Status === ENUMS::CHECK_DETAIL_STATUS_HAS_VOIDED) {
            throw new \CSException('Check details with a status of void cannot be deleted!');
        }

        // disallow deletes on check detail when check status is not open
        $checkId = $checkDetail->CheckID;
        $checkUow = UnitOfWork::build()->action('get')->table('checks')->table_id($checkId);
        $this->logic->checks->uow($checkUow);
        $check = $checkUow->data;
        if ($check->CheckStatus !== ENUMS::CHECK_STATUS_OPEN) {
            throw new \CSException('Check details attached to a check which is not open cannot be deleted!');
        }
    }

    public final function create($params = array()) {
        // this doesn't get hit when we swapped to UOW. have to refactor.
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
            if ($product->ProductType === Enums::PRODUCT_TYPE_GIFT_CARD && $checkDetails->Qty + $checkDetails->CadetQty !== 1)
                throw new \CSException("CheckDetails create with a gift card product must have a quantity of 1! Received Qty: " . $checkDetails->Qty . " and CadetQty: " . $checkDetails->CadetQty);
            $checkDetails->TaxID = $tax->TaxID;
            $checkDetails->TaxPercent = $tax->Amount;
            $checkDetails->Status = Enums::CHECK_DETAIL_STATUS_IS_NEW;
            $checkDetails->CreatedDate = Convert::getDate();
            $checkDetails->ProductName = $product->Description;
            $checkDetails->UnitPrice = $product->Price1;
            $checkDetails->UnitPrice2 = $product->Price2;
            $checkDetails->Type = $product->ProductType; // seems to be the product type -- DOUBLE CHECK
            $checkDetails->GST = $tax->GST;
            $checkDetails->P_Points = $product->P_Points; // null P_Points is reproduceable with a non point product on the front end
            if (!is_null($checkDetails->P_Points) && $checkDetails->P_Points > 0)
                $checkDetails->P_CustID = $check->CustID;
            $checkDetails->R_Points = $product->R_Points; // we want nulls to stay null, don't convert to 0
            $checkDetails->G_Points = $product->G_Points; // leave G_Points as null as well
            return $checkDetails;
        });
    }
}
