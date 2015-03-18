<?php

namespace ClubSpeed\Payments;
use Omnipay\Omnipay;

/**
 * The database interface class
 * for ClubSpeed online booking.
 */
class PaymentService {

    private $logic;
    public $handlers;
    private $_lazy;
    private $allowed;

    public function __construct(&$CSLogic) {
        $this->logic = $CSLogic;
        $this->_lazy = array();
        $this->allowed = array(
            'Dummy',
            'SagePay_Direct',
            'Payflow_Pro',
            'PayPal_Pro',
            'PCCharge',
            'WorldPayXML',
            'AuthorizeNet_AIM'
            // add processors as we test/support them -- dummy should ABSOLUTELY be removed before going live
        );
    }

    public function available() {
        $processors = array_intersect(Omnipay::find(), $this->allowed); // necessary? we could just use the allowed array, too.
        $available = array();
        $stuff = array_walk($processors, function($val, $key) use (&$available) {
            $processor = Omnipay::create($val);
            $available[] = array(
                "name"      => $val,
                "options"   => array_keys($processor->getParameters())
            );
        });
        return $available;
    }

    function __get($prop) {
        return $this->load($prop);
    }

    private function load($prop) {
        $prop = '\ClubSpeed\Payments\\' . ucfirst($prop) . 'Payment';
        if (!isset($this->_lazy[$prop])) {
            $this->_lazy[$prop] = new $prop($this->logic, $this);
        }
        return $this->_lazy[$prop];
    }
}