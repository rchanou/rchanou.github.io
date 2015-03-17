<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert as Convert;

/**
 * The business logic class
 * for ClubSpeed check totals.
 */
class CheckTotalsLogic extends BaseLogic {

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
            if ($calc[$this->interface->keys[0]] != $tempCheckId) {
                // we need to create a new check -- this is either the first checkTotals record, or the client passed multiple checks
                $check = $this->logic->checks->dummy($calc);
                $tempCheckId = $check->CheckID;
                $check->CheckID = null;
                $checkCreateData = $this->logic->checks->create($check);
                $actualCheckId = $checkCreateData[$this->interface->keys[0]];
            }
            $calc[$this->interface->keys[0]] = $actualCheckId; // tie the new details record back to the new CheckID
            $checkDetailId = $this->logic->checkDetails->create($calc);
        }
        // $this->logic->checks->applyCheckTotal($actualCheckId); // check create is now automatically running the applyCheckTotal stored proc
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
        $discount = 0;
        if (!empty($giftCards)) {
            foreach($giftCards as $giftCardId) {
                $giftCardCustomer = $this->logic->customers->find("CrdID = " . $giftCardId . " AND IsGiftCard = True");
                if (empty($giftCardCustomer))
                    throw new \RecordNotFoundException("Unable to find gift card: " . $giftCardId);
                $giftCardCustomer = $giftCardCustomer[0];
                $giftCardHistory = $this->logic->giftCardHistory->find("CustID = " . $giftCardCustomer->CustID);
                if (empty($giftCardHistory))
                    throw new \RecordNotFoundException("Unable to find gift card by CustID: " . $giftCardCustomer->CustID);
                $giftCardHistory = $giftCardHistory[0];
                // consider the giftCardHistory Points to be part of a discount
                // note that giftCardHistory->Points are not actually points,
                // they are monetary currency (db field misnamed and never fixed?)
                $discount += $giftCardHistory->Points;
            }
        }
        if (!is_array($params))
            $params = array($params); // for foreach syntax
        foreach($params as $key => $param) {
            $checkTotals = $this->interface->dummy($param);
            // consider the giftCard total a discount?
            // altering the checkTotal based on potential future gift card payments
            // is hacky no matter where we put it, may as well use this for now
            $checkTotals->Discount = $discount; // consider using CheckPaidTotal instead of Discount, now that the column is available
            $params[$key] = $checkTotals;
        }
        $calculated = $this->applyCheckTotal($params);
        return $calculated;
    }

    private function applyCheckTotal($checks = array()) {
        $running = array(); // keep track of running totals and taxes on a checkId basis
        $products = array(); // keep track of products by productId
        $taxes = array(); // keep track of taxes by taxId

        $useSalesTax = $this->logic->helpers->useSalesTax();

        foreach($checks as $check) {
            if (!isset($products[$check->ProductID])) {
                if (!isset($check->ProductID))
                    throw new \CSException("CheckTotal Virtual requires a productId for every check details! Received ProductID: " . $check->ProductID);
                $product = $this->db->products->get($check->ProductID);
                if (is_null($product) || empty($product))
                    throw new \RecordNotFoundException("CheckTotal Virtual received a ProductID which could not be found! Received ProductID: " . $check->ProductID);
                $product = $product[0];
                $products[$check->ProductID] = $product;
            }
            $product = $products[$check->ProductID];
            if (!isset($taxes[$product->TaxID])) {
                $tax = $this->db->taxes->get($product->TaxID);
                if (is_null($tax) || empty($tax)) // this should really never happen
                    throw new \RecordNotFoundException("CheckTotal Virtual received a ProductID which had a TaxID that could nto be found! Received TaxID: " . $product->TaxID);
                $tax = $tax[0];
                $taxes[$product->TaxID] = $tax;
            }
            $tax = $taxes[$product->TaxID];

            // store items required for Check/CheckDetails storage
            $check->TaxID = $tax->TaxID;
            $check->GST = $tax->GST;
            $check->TaxPercent = $tax->Amount;

            // begin logic found in VB
            $check->DiscountApplied = 0; // just assume the DiscountApplied will be 0 with virtual/posted checks
            $check->UnitPrice = $product->Price1;
            $checkDetailActualQuantity = ($check->Qty ?: 0) + ($check->CadetQty ?: 0);
            $check->CheckDetailSubtotal = $check->UnitPrice * $checkDetailActualQuantity;
            if ($useSalesTax) {
                $compoundTaxRate = (((1 + $tax->GST / 100) * (1 + ($tax->Amount - $tax->GST) / 100)) - 1) * 100; // calculation taken from WebAPI
                $checkDetailSingleTaxAmount = round($check->UnitPrice * ($compoundTaxRate / 100.0), 2);
                $check->CheckDetailTax = $checkDetailSingleTaxAmount * $checkDetailActualQuantity;
                $check->CheckDetailTotal = $check->CheckDetailSubtotal + $check->CheckDetailTax;
            }
            else { // assume VAT
                $checkDetailSingleTaxAmount = round($check->UnitPrice * ($tax->Amount / 100.0), 2);
                $check->CheckDetailTax = $checkDetailSingleTaxAmount * $checkDetailActualQuantity;
                $check->CheckDetailTotal = $check->CheckDetailSubtotal;
            }
            if (!isset($running[$check->CheckID])) {
                $running[$check->CheckID] = array(
                      'subtotal'    => 0
                    , 'tax'         => 0
                );
            }
            $running[$check->CheckID]['subtotal'] += $check->CheckDetailSubtotal;
            $running[$check->CheckID]['tax'] += $check->CheckDetailTax;
        }
        foreach($checks as $check) {
            $check->CheckSubtotal = $running[$check->CheckID]['subtotal'];
            $check->CheckTax = $running[$check->CheckID]['tax'];
            if ($useSalesTax) {
                $check->CheckTotal = $check->CheckSubtotal + $check->CheckTax;
            }
            else { // assume VAT
                $check->CheckTotal = $check->CheckSubtotal;
            }
            $check->CheckTotal -= $check->Discount;

            // for virtual, assume there are no outstanding payments (may need to change in future)
            $check->CheckPaidTax = 0;
            $check->CheckPaidTotal = 0;
            $check->CheckRemainingTax = $check->CheckTax;
            $check->CheckRemainingTotal = $check->CheckTotal;
        }
        return $checks;
    }
}