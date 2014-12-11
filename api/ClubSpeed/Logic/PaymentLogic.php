<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed payments.
 */
class PaymentLogic extends BaseLogic {

    /**
     * Constructs a new instance of the PaymentLogic class.
     *
     * The PaymentLogic constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the LogicContainer from which this class will been loaded.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->payment;
    }

    public function create($params = array()) {
        $db =& $this->db;
        return parent::_create($params, function($payment) use (&$db) {

            // more todo

            if (is_null($payment->VoidTerminal))
                $payment->VoidTerminal = '';
            if (is_null($payment->ExternalAccountNumber))
                $payment->ExternalAccountNumber = ''; // front end logs error if this is set to null
            if (is_null($payment->ExternalAccountName))
                $payment->ExternalAccountName = ''; // front end logs error if this is set to null, and payment type is 3 -- just default to empty

            return $payment;
        });
    }
}