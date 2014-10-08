<?php

namespace ClubSpeed\Payments;

use Omnipay\Omnipay;

require_once(__DIR__.'/CSPaymentsBase.php');

class CSPaymentsSagePayDirect extends CSPaymentsBase {

    public function __construct(&$CSPayments, &$CSLogic) {
        parent::__construct($CSPayments, $CSLogic);
        $this->gateway = Omnipay::create('SagePay_Direct'); // hard coded
        $this->gateway->initialize(array(
            'vendor' => 'clubspeed3',
            'simulatorMode' => true
        ));
    }

    // public function mutateResponse($response) {
    //     $reference = json_decode($response->getTransactionReference());
    //     return array(
    //           'checkId'         => $reference->VendorTxCode
    //         , 'checkAuthId'     => $reference->TxAuthNo // THIS IS SAGEPAY SPECIFIC -- HANDLE DIFFERENTLY
    //         , 'vendorCheckId'   => $reference->VPSTxId
    //     );
    // }
}