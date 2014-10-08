<?php

namespace ClubSpeed\Logic;

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
            if ($calc[$this->interface->key] != $tempCheckId) {
                // we need to create a new check -- this is either the first checkTotals record, or the client passed multiple checks
                $check = $this->logic->checks->dummy($calc);
                $tempCheckId = $check->CheckID;
                $check->CheckID = null;
                $checkCreateData = $this->logic->checks->create($check);
                $actualCheckId = $checkCreateData[$this->interface->key];
            }
            $calc[$this->interface->key] = $actualCheckId; // tie the new details record back to the new CheckID
            $checkDetailId = $this->logic->checkDetails->create($calc);
        }
        $this->logic->checks->applyCheckTotal($actualCheckId); // run the applyCheckTotal stored procedure, not the virtual one
        return $checkCreateData;
    }

    public final function update($id, $params = array()) {
        throw new \CSException("Attempted a CheckTotals update!");
    }

    public final function delete($id) {
        throw new \CSException("Attempted a CheckTotals delete!");
    }

    public function virtual($params = array()) {
        if (!is_array($params))
            $params = array($params); // for foreach syntax
        foreach($params as $key => $param) {
            $params[$key] = $this->interface->dummy($param);
        }
        $calculated = $this->applyCheckTotal($params);
        return $calculated;
    }

    private function applyCheckTotal($checks = array()) {
        $running = array(); // keep track of running totals and taxes on a checkId basis
        $products = array(); // keep track of products by productId
        $taxes = array(); // keep track of taxes by taxId

        $useSalesTax =$this->logic->helpers->useSalesTax();
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
            $check->DiscountApplied = 0; // equivalent to cartDetail.Discount (?)
            $check->UnitPrice = $product->Price1; // for the return's sake
            $check->CheckDetailSubtotal = $check->Qty * $product->Price1 - $check->DiscountApplied; // equivalent to cartDetail.SubTotal
            $compoundTaxRate = (((1 + $tax->GST / 100) * (1 + ($tax->Amount - $tax->GST) / 100)) - 1) * 100; // calculation taken from WebAPI
            if ($useSalesTax) {
                // totalGST = round(($check->CheckDetailSubtotal * $tax->GST) / (100.0), 2);
                $check->CheckDetailTax = round((($check->CheckDetailSubtotal * $compoundTaxRate) / 100.0), 2);
                $check->CheckDetailTotal = $check->CheckDetailSubtotal + $check->CheckDetailTax;
            }
            else { // assume VAT
                // totalGST = round(($check->CheckDetailTotal * $tax->GST) / (100.0 + $tax->GST), 2);
                $check->CheckDetailTax = round((($check->CheckDetailSubtotal * $tax->Amount) / (100 + $tax->Amount)), 2);
                $check->CheckDetailTotal = $check->CheckDetailSubtotal;
            }
            if (!isset($running[$check->CheckID])) {
                $running[$check->CheckID] = array(
                    'subtotal' => 0,
                    'tax' => 0
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
        }
        return $checks;
    }
}