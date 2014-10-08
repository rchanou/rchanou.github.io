<?php

namespace ClubSpeed\Payments;

use Omnipay\Omnipay;

require_once(__DIR__.'/CSPaymentsBase.php');

class CSPaymentsPayPalExpress extends CSPaymentsBase {

    public function __construct(&$CSPayments, &$CSLogic) {
        parent::__construct($CSPayments, $CSLogic);
        $this->gateway = Omnipay::create('PayPal_Express');
        $this->gateway->setUsername('devs-facilitator_api1.clubspeed.com');
        $this->gateway->setPassword('1374792775');
        $this->gateway->setSignature('Az.5WFX1fsABe8KxOBx79hVKHLISAWNwCxiHedq83lAxDked2VezNrqM');
        $this->gateway->setTestMode(true);
        $this->namespace = 'payPalExpress';

        // test credentials
        // Classic TEST API credentials
        // Username: devs-facilitator_api1.clubspeed.com
        // Password: 1374792775
        // Signature: Az.5WFX1fsABe8KxOBx79hVKHLISAWNwCxiHedq83lAxDked2VezNrqM

        // live credentials (?)
        // Credential  API Signature
        // API Username    devs_api1.clubspeed.com
        // API Password    ET79EDWWHBXJ7C6G
        // Signature       AV027oWxS8Dqu9Z5zBMyzku8rSFqAPj4Tb53iJ9WEmZJGpQSBufNaMQz
        // Request Date    Sep 23, 2014 15:14:44 PDT
    }
}