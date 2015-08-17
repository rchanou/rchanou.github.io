<?php

namespace ClubSpeed\Payments;
use Omnipay\Omnipay;

/**
 * The database interface class
 * for ClubSpeed online booking.
 */
class PaymentService {

    private $logic;
    private $db;
    public $handlers;
    private $_lazy;
    private $allowed;

    public function __construct(&$CSLogic, &$db) {
        $this->logic = $CSLogic;
        $this->db = $db;
        $this->_lazy = array();
        $this->allowed = array(
            'Dummy',
            'AuthorizeNet_AIM',
            'Payflow_Pro',
            'PayPal_Pro',
            'SagePay_Direct',
            'WorldPayXML'
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
            $this->_lazy[$prop] = new $prop($this->logic, $this->db, $this);
        }
        return $this->_lazy[$prop];
    }
}