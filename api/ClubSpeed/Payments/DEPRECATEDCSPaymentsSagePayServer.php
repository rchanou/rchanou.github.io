<?php

namespace ClubSpeed\Payments;

use Omnipay\Omnipay;

require_once(__DIR__.'/CSPaymentsBase.php');

class CSPaymentsSagePayServer extends CSPaymentsBase {

    public function __construct(&$CSPayments, &$CSLogic) {
        parent::__construct($CSPayments, $CSLogic);

        $this->gateway = Omnipay::create('SagePay_Server'); // hard coded
        $this->gateway->setVendor('clubspeed3'); // hard coded
        $this->gateway->setSimulatorMode(true); // only during debugging -- needs to be false when going live
        $this->namespace = 'sagePayServer';
    }
}