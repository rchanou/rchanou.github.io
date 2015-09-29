<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert as Convert;
use ClubSpeed\Enums\Enums;

/**
 * The business logic class
 * for ClubSpeed check totals.
 */
class CheckTotalsLogic extends BaseLogic {

    private $_discounts;

    /**
     * Constructs a new instance of the CheckTotalsLogic class.
     *
     * The CheckTotalsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->checkTotals_V;

        $this->_discounts = array();
        
        // any others?
        $this->insertable = array( 
            'CustID',
            'ProductID',
            'Qty'
        );
    }

    public final function create($params = array()) {
        // completely override, since we need to insert check and checkdetails records separately
        foreach($params as $param) {
            $calculated[] = $this->interface->dummy($param);
        }
        $calculated = $this->applyCheckTotal($calculated);
        $tempCheckId = -1;
        $checkCreateData = null;
        foreach($calculated as $calc) {
            $calc = (array)$calc; // convert to array for check and check detail creation
            $keyname = $this->interface->keys[0]['name'];
            if ($calc[$keyname] != $tempCheckId) {
                // we need to create a new check -- this is either the first checkTotals record, or the client passed multiple checks
                $check = $this->logic->checks->dummy($calc);
                // map columns manually for any check discounts
                $check->DiscountID     = $calc['CheckDiscountID'];
                $check->DiscountNotes  = $calc['CheckDiscountNotes'];
                $check->DiscountUserID = $calc['CheckDiscountUserID'];
                $tempCheckId = $check->CheckID;
                $check->CheckID = null;
                $checkCreateData = $this->logic->checks->create($check);
                $actualCheckId = $checkCreateData[$keyname];
            }
            $calc[$keyname] = $actualCheckId; // tie the new details record back to the new CheckID

            $checkDetail = $this->logic->checkDetails->dummy($calc);
            // map columns manually for any line discounts
            $checkDetail->DiscountID     = $calc['CheckDetailDiscountID'];
            $checkDetail->DiscountNotes  = $calc['CheckDetailDiscountNotes'];
            $checkDetail->CalculateType  = $calc['CheckDetailDiscountCalculateType'];
            $checkDetail->DiscountUserID = $calc['CheckDetailDiscountUserID'];
            $checkDetail->DiscountDesc   = $calc['CheckDetailDiscountDesc'];
            $checkDetailId = $this->logic->checkDetails->create($checkDetail);
        }
        $this->logic->checks->applyCheckTotal($actualCheckId);
        return $checkCreateData;
    }

    public final function update() {
        throw new \CSException("Attempted a CheckTotals update!");
    }

    public final function delete() {
        throw new \CSException("Attempted a CheckTotals delete!");
    }

    public function virtual($params = array(), $giftCards = array()) {
        // hack the gift card logic in here
        // note that giftcards ids provided must match up with dbo.Customers.CrdID
        // and then that record's CustID should be used to find the GiftCardHistoryID
        // (odd, but this is the expectation for the database's data)
        $paid = 0;
        if (!empty($giftCards)) {
            foreach($giftCards as $giftCardId) {
                $giftCardBalance = $this->logic->giftCardBalance->find("CrdID = " . $giftCardId);
                if (empty($giftCardBalance))
                    throw new \RecordNotFoundException('GiftCardBalance_V', $giftCardCustomer->CustID);
                $giftCardBalance = $giftCardBalance[0];
                $paid += $giftCardBalance->Money; // or do we use Points?
            }
        }
        if (!is_array($params))
            $params = array($params); // for foreach syntax. we're only accepting one check now, so this is kind of worthless, but just to be safe.
        foreach($params as $key => $param) {
            $checkTotals = $this->interface->dummy($param);
            $checkTotals->CheckPaidTotal = $paid; // note that now we only have 1 check being accepted, the gift cards will only line up to 1 check, so this is still technically safe, if awkward.
            $params[$key] = $checkTotals;
        }
        $calculated = $this->applyCheckTotal($params);
        return $calculated;
    }

    private function loadDiscount($discountId) {
        // re-useable local caching system?
        if (!isset($this->_discounts[$discountId])) {
            $discount = $this->db->discountType->get($discountId);
            if (is_null($discount) || empty($discount))
                throw new \RecordNotFoundException('DiscountType', $check->CheckDetailDiscountID);
            $discount = $discount[0];
            $this->_discounts[$discountId] = $discount;
        }
        return $this->_discounts[$discountId];
    }

    private static function calculateDiscount($discount, $checkAmount) {
        // or does this belong in DiscountTypeLogic?
        $calculatedDiscount = 0.0;
        if ($discount->CalculateType === Enums::CALCULATE_TYPE_AMOUNT) {
            if ($discount->Amount > $checkAmount)
                $calculatedDiscount = $checkAmount; // don't give discounts greater than total
            else
                $calculatedDiscount = $discount->Amount;
        }
        else /* CALCULATE_TYPE_PERCENT */ {
            if ($discount->Amount >= 100)
                $calculatedDiscount = $checkAmount; // again, don't give discounts greater than total
            else
                $calculatedDiscount = round($discount->Amount / 100.0 * $checkAmount, 2);
        }
        return $calculatedDiscount;
    }

    private function applyCheckTotal($checks = array()) {
        $running = array(); // keep track of running totals and taxes on a checkId basis
        $products = array(); // keep track of products by productId
        $taxes = array(); // keep track of taxes by taxId
        $discounts = array(); // keep track of discounts by discountId

        $useSalesTax = $this->logic->helpers->useSalesTax();

        // when we have a gift card with quantity > 1,
        // then break the qty into multiple line items 
        // each with a quantity of only 1
        $checksExpanded = array();
        $count = count($checks);
        for ($i = 0; $i < $count; $i++) {
            $check = $checks[$i];
            if (!isset($check->ProductID))
                throw new \CSException("CheckTotal requires a productId for every detail! Received ProductID: " . $check->ProductID);
            $product = $this->db->products->get($check->ProductID);
            if (is_null($product) || empty($product))
                throw new \RecordNotFoundException('Products', $check->ProductID);
            $product = $product[0];
            $products[$check->ProductID] = $product;
            if ($check->Qty + $check->CadetQty < 2 || $product->ProductType !== Enums::PRODUCT_TYPE_GIFT_CARD) {
                $checksExpanded[] = $check;
            }
            else {
                for($j = 0; $j < ($check->Qty + $check->CadetQty); $j++) {
                    $clone = $this->interface->dummy((array)$check);
                    $clone->Qty = 1;
                    $clone->CadetQty = 0; // scary?
                    $checksExpanded[] = $clone;
                }
            }
        }

        foreach($checksExpanded as $check) {
            $check->UserID = $check->UserID ?: 1; // necessary to duplicate the fallback logic here, unfortunately.

            if (!isset($products[$check->ProductID])) {
                if (!isset($check->ProductID))
                    throw new \CSException("CheckTotal Virtual requires a productId for every check details! Received ProductID: " . $check->ProductID);
                $product = $this->db->products->get($check->ProductID);
                if (is_null($product) || empty($product))
                    throw new \RecordNotFoundException('Products', $check->ProductID);
                $product = $product[0];
                $products[$check->ProductID] = $product;
            }
            $product = $products[$check->ProductID];
            if (!isset($taxes[$product->TaxID])) {
                $tax = $this->db->taxes->get($product->TaxID);
                if (is_null($tax) || empty($tax)) // this should really never happen
                    throw new \RecordNotFoundException('Taxes', $product->TaxID);
                $tax = $tax[0];
                $taxes[$product->TaxID] = $tax;
            }
            $tax = $taxes[$product->TaxID];

            // store items required for Check/CheckDetails storage
            $check->TaxID             = $tax->TaxID;
            $check->GST               = $tax->GST;
            $check->TaxPercent        = $tax->Amount;
            $check->UnitPrice         = $product->Price1;
            $check->ProductName       = $product->Description;
            $check->CheckDetailStatus = Enums::CHECK_DETAIL_STATUS_IS_NEW;
            $check->CheckStatus       = Enums::CHECK_STATUS_OPEN;
            $check->CheckDetailType   = $product->ProductType;
            $check->CheckType         = Enums::CHECK_TYPE_REGULAR; // should we allow events or "show all" (whatever that is) with virtual?

            $checkDetailActualQuantity = ($check->Qty ?: 0) + ($check->CadetQty ?: 0);

            // if there are check detail level discounts, look them up before determining subtotal to match VB logic
            $checkDetailSingleDiscountAmount = 0;
            if (isset($check->CheckDetailDiscountID)) {
                $discount = $this->loadDiscount($check->CheckDetailDiscountID);
                $check->CheckDetailDiscountCalculateType = $discount->CalculateType;
                $check->CheckDetailDiscountDesc = $discount->Description;
                $check->CheckDetailDiscountUserID = $check->UserID;
                $checkDetailSingleDiscountAmount = self::calculateDiscount($discount, $check->UnitPrice);
                $check->DiscountApplied = $checkDetailSingleDiscountAmount * $checkDetailActualQuantity;
            }
            else
                $check->DiscountApplied = 0; // ensure the return is always a number

            $check->Gratuity = $check->Gratuity ?: 0;
            $check->Fee      = $check->Fee ?: 0;
            // $check->Discount        = $check->Discount ?: 0;
            // $check->DiscountApplied = $check->DiscountApplied ?: 0; // ensure the return is always a number

            // note that $check->Discount is at the check level, and should always be stored as an amount
            // as well that $check->DiscountApplied is at the single line item level, will always be stored as an amount
            $checkDetailSingleAmount   = $check->UnitPrice - $checkDetailSingleDiscountAmount;
            $checkDetailSubtotal       = $checkDetailSingleAmount * $checkDetailActualQuantity;
            $checkDetailTaxPercentage  = $check->TaxPercent / 100.0;
            $checkDetailGSTPercentage  = $check->GST / 100.0;
            if ($useSalesTax) {
                $checkDetailSingleTaxAmount = round($checkDetailSingleAmount * $checkDetailTaxPercentage, 2);
                $checkDetailSingleGSTAmount = round($checkDetailSingleAmount * $checkDetailGSTPercentage, 2);
                $checkDetailSinglePSTAmount = $checkDetailSingleTaxAmount - $checkDetailSingleGSTAmount;
                $checkDetailTax             = $checkDetailSingleTaxAmount * $checkDetailActualQuantity;
                $checkDetailGST             = $checkDetailSingleGSTAmount * $checkDetailActualQuantity;
                $checkDetailPST             = $checkDetailSinglePSTAmount * $checkDetailActualQuantity;
                $checkDetailTotal           = $checkDetailSubtotal + $checkDetailTax;
            }
            else {
                $checkDetailSingleTaxAmount = $checkDetailSingleAmount - round($checkDetailSingleAmount / (1.0 + $checkDetailTaxPercentage), 2);
                $checkDetailTax = $checkDetailSingleTaxAmount * $checkDetailActualQuantity;
                $checkDetailGST = 0; // GST not relevant for VAT
                $checkDetailPST = 0; // PST not relevant for VAT
                $checkDetailTotal = $checkDetailSubtotal; // Total == Subtotal with VAT
            }
            $check->CheckDetailTax      = $checkDetailTax;
            $check->CheckDetailGST      = $checkDetailGST;
            $check->CheckDetailPST      = $checkDetailPST;
            $check->CheckDetailSubtotal = $checkDetailSubtotal;
            $check->CheckDetailTotal    = $checkDetailTotal;

            if (!isset($running[$check->CheckID])) {
                $running[$check->CheckID] = array(
                      'subtotal' => 0
                    , 'tax'      => 0
                    , 'pst'      => 0
                    , 'gst'      => 0
                );
            }
            $running[$check->CheckID]['subtotal'] += $check->CheckDetailSubtotal;
            $running[$check->CheckID]['tax'] += $check->CheckDetailTax;
            $running[$check->CheckID]['gst'] += $check->CheckDetailGST;
            $running[$check->CheckID]['pst'] += $check->CheckDetailPST;
        }

        foreach($checksExpanded as $check) {
            $check->CheckSubtotal = $running[$check->CheckID]['subtotal']; // + $check->Fee + $check->Gratuity - $check->Discount;
            $check->CheckTax = $running[$check->CheckID]['tax'];
            $check->CheckGST = $running[$check->CheckID]['gst'];
            $check->CheckPST = $running[$check->CheckID]['pst'];
            $check->CheckTotal = $check->CheckSubtotal; // start with the subtotal
            if ($useSalesTax)
                $check->CheckTotal += $check->CheckTax;
            $check->CheckTotal += $check->Gratuity;
            $check->CheckTotal += $check->Fee;

            if (isset($check->CheckDiscountID)) {
                $discount = $this->loadDiscount($check->CheckDiscountID);
                $check->CheckDiscountNotes = $discount->Description;
                $check->CheckDiscountUserID = $check->UserID;
                $check->Discount = self::calculateDiscount($discount, $check->CheckTotal);
            }
            else
                $check->Discount = 0;
            $check->CheckTotal -= $check->Discount;

            // how to handle CheckPaidTax? Apply CheckPaidTotal toward the Tax until the Tax is gone? Other way around? Try to maintain percentages??
            $check->CheckPaidTax = $check->CheckPaidTax ?: 0;
            $check->CheckPaidTotal = $check->CheckPaidTotal ?: 0;
            if ($check->CheckPaidTotal > $check->CheckTotal)
                $check->CheckPaidTotal = $check->CheckTotal; // for gift cards?
            $check->CheckRemainingTax = $check->CheckTax - $check->CheckPaidTax; // can we deprecate this? brian isn't using it. not really sure how to get a reliable % out of this, if its not 100% covered by gift cards or discounts.
            if ($check->CheckRemainingTax < 0)
                $check->CheckRemainingTax = 0;
            $check->CheckRemainingTotal = $check->CheckTotal - $check->CheckPaidTotal;
            if ($check->CheckRemainingTotal < 0)
                $check->CheckRemainingTotal = 0; // these could actually be negatives with refunds. careful, if we need to support that.
        }

        return $checksExpanded;
    }
}
