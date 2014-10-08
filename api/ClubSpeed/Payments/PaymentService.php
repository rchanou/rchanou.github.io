<?php

namespace ClubSpeed\Payments;

/**
 * The database interface class
 * for ClubSpeed online booking.
 */
class PaymentService {

    private $logic;
    public $handlers;
    private $_lazy;

    public function __construct(&$CSLogic) {
        $this->logic = $CSLogic;
        $this->handlers = new \ClubSpeed\Payments\ProductHandlers\ProductHandlerService($this, $this->logic);
        $this->_lazy = array();
    }

    function __get($prop) {
        return $this->load($prop);
    }

    private function load($prop) {
        $prop = '\ClubSpeed\Payments\\' . ucfirst($prop) . 'Payment';
        if (!isset($this->_lazy[$prop])) {
            $this->_lazy[$prop] = new $prop($this->logic, $this->handlers);
        }
        return $this->_lazy[$prop];
    }
}