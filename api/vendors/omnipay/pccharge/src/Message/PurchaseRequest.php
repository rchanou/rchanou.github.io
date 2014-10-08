<?php

namespace Omnipay\PCCharge\Message;

/**
 * PCCharge Purchase Request
 */
class PurchaseRequest extends AuthorizeRequest
{
    // public function getData()
    // {
    //     $data = parent::getData();
    //     // $data['capture'] = 'true';

    //     //// below is the full set of requirements for PCCharge WebAPI remoting call
    //     // $expiry = str_pad($card['expiryMonth'], 2, '0', STR_PAD_LEFT) . substr($card['expiryYear'], -2);
    //     // $data = array(
    //     //     "CreditCardNo"      => $card['number']
    //     //     , 'AccountName'     => $card['firstName'] . ' ' . $card['lastName'] // concatenate first + last before sending to webapi
    //     //     , 'ExpirationDate'  => $expiry // this needs to be in 'MMYY' string format
    //     //     , 'Zip'             => $card['postcode']
    //     //     , 'Address'         => $card['address1']
    //     //     , 'CVV'             => $card['cvv']
    //     //     , 'CheckID'         => $checkId
    //     //     , 'CardIssuer'      => ''       // unknown
    //     //     , 'TaxExempt'       => false    // override to false
    //     //     , 'IsCommercial'    => false    // override to false
    //     //     , 'TaxAmount'       => $checkTotals->CheckTax // this is not available directly on the check record - grab from the calculated view
    //     //     , 'AmountToCharge'  => 0.01 // USE 2.00 FOR PCCHARGE TESTING - should come back declined $check->CheckTotal // use the total (INCLUDING THE TAX, NOT THE SUBTOTAL!!!)
    //     // );

    //     return $data;
    // }

    public function getEndpoint()
    {
        return $this->endpoint.'/ProcessPayment';
    }
}
