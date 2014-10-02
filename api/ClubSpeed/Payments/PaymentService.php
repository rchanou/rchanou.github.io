<?php

namespace ClubSpeed\Payments;

use Omnipay\Omnipay;

/**
 * The database interface class
 * for ClubSpeed online booking.
 */
class PaymentService {

    private $logic;
    private $gateway;
    private $intlCurrencySymbol;

    public function __construct(&$CSLogic) {
        $this->logic = $CSLogic;
    }

    protected function init($data) {
        $this->gateway = Omnipay::create(@$data['name']);
        $this->gateway->initialize(@$data['options']);
    }

    public function handleSuccess($check, $response) {
        // do something!
        $check->CheckStatus = 1; // CheckStatus.Closed from VB enum
        $check->ClosedDate = \ClubSpeed\Utility\Convert::getDate();
        $this->logic->checks->update($check->CheckID, $check);
        return;
    }

    public function handleRedirect($check, $response) {
        return array(
            'redirect' => array(
                'url'       => $response->getRedirectUrl(),
                'method'    => $response->getRedirectMethod(),
                'data'      => $response->getRedirectData()
                // 'reference' => $response->getTransactionReference() // client probably doesn't need this info, and will most likely get it from the payment processor (?)
            )
        );
    }

    public function handleFailure($check, $response) {
        return array(
            'error' => array(
                'message' => $response->getMessage()
                , 'data' => $response->getData()
            )
        );
    }

    protected function passthroughWrapper($params, $callback) {
        // assume params need to be send directly through gateway->completeRequest without being modified

    }

    protected function wrapper($params, $callback) {
        $this->init($params);
        $checkId = \ClubSpeed\Utility\Convert::toNumber(@$params['checkId']);
        if (!isset($checkId) || is_null($checkId) || !is_int($checkId))
            throw new \RequiredArgumentMissingException("Payment processor received an invalid format for checkId! Received: " . @$params['checkId']);
        $checkTotals = $this->logic->checkTotals->get($checkId);
        if (!isset($checkTotals) || is_null($checkTotals) || empty($checkTotals))
            throw new \InvalidArgumentValueException("Payment processor received a checkId which could not be found in the database! Received: " . $checkId);
        $checkTotals = $checkTotals[0];
        if ($checkTotals->CheckStatus != 0)
            throw new \InvalidArgumentValueException("Payment processor received a checkId with a status other than 0 (open)! Found Check.Status: " . $checkTotals->CheckStatus);
        $this->logic->checks->applyCheckTotal($checkTotals->CheckID); // ensure that the checktotal is stored on the checks record, for backwards compatibility

        $options = array(
            'amount'                 => $checkTotals->CheckTotal // this is why we grabbed the CheckTotals_V object instead of the Checks object
            , 'currency'             => $this->getIntlCurrencySymbol() // THIS REQUIRES php_intl.dll EXTENSION TURNED ON
            , 'description'          => "ClubSpeed payment for CheckID: " . $checkTotals->CheckID // build description manually?
            , 'transactionId'        => $checkTotals->CheckID
            , 'transactionReference' => 'some_transaction_reference' // use session id from laravel?
            , 'cardReference'        => 'some_card_reference'
            , 'returnUrl'            => $this->getReturnUrl()
            , 'cancelUrl'            => $this->getCancelUrl()
            , 'notifyUrl'            => $this->getNotifyUrl()
            , 'issuer'               => ''
            , 'card'                 => new \Omnipay\Common\CreditCard(@$params['card'])
            , 'clientIp'             => $this->getIp() // use the api ip? or the client ip? or the middle-tier ip?
        );

        $response = $callback($options);

        $check = $this->logic->checks->get($checkId);
        $check = $check[0];
        $check->Notes = $response->getTransactionReference(); // store the tx reference in Checks.Notes, since we don't have another field to use at this time
        $this->logic->checks->update($check->CheckID, $check);

        if ($response->isSuccessful())
            return $this->handleSuccess($check, $response);
        else if ($response->isRedirect())
            return $this->handleRedirect($check, $response);
        else
            return $this->handleFailure($check, $response);
    }

    // todo: support this stuff later -- just handle purchase for now
    // public function authorize($params = array()) {
    //     if (!$this->gateway->supportsAuthorize())
    //         throw new \UnsupportedMethodException("This gateway does not support the Authorize call!");
    //     return $this->wrapper($params, function($options) {
    //         $response = $this->gateway->authorize($options)->send();
    //     });
    // }

    public function purchase($params = array()) {
        $gateway =& $this->gateway;
        return $this->wrapper($params, function($options) use (&$gateway) {
            return $gateway->purchase($options)->send();
        });
    }

    public function completePurchase($params = array()) {
        // this is sort of its own beast -- do we want to use the wrapper? or a secondary wrapper?
        // do we assume that $params are already okay to send on to completePurchase (?)
        $response = $this->gateway->completePurchase($params)->send();
        $checks = $this->logic->checks->match(array('Notes' => $response->getTransactionReference())); // find the check with the notes that match the transactionReference
        $check = $checks[0];

        if ($response->isSuccessful())
            return $this->handleSuccess($check, $response);
        else if ($response->isRedirect())
            return $this->handleRedirect($check, $response); // this probably shouldn't happen
        else
            return $this->handleFailure($check, $response);
    }

    protected function getIp() {
        return getHostByName(php_uname('n'));
    }

    protected function getReturnUrl() {
        return 'http://' . $this->getIp() . "/api/index.php/payments/"; // TODO: get actual return url
    }

    protected function getNotifyUrl() {
        return 'http://' . $this->getIp() . "/notify/"; // TODO: get actual return url
    }

    protected function getCancelUrl() {
        return 'http://' . $this->getIp() . "/cancel/"; // TODO: get actual return url
    }

    protected function getTestCard() {
        return array(
            'firstName'        => 'Example',
            'lastName'         => 'User',
            'number'           => '4111111111111111',
            'expiryMonth'      => rand(1, 12),
            'expiryYear'       => gmdate('Y') + rand(1, 5),
            'cvv'              => rand(100, 999),
            'billingAddress1'  => '123 Billing St',
            'billingAddress2'  => 'Billsville',
            'billingCity'      => 'Billstown',
            'billingPostcode'  => '12345',
            'billingState'     => 'CA',
            'billingCountry'   => 'US',
            'billingPhone'     => '(555) 123-4567',
            'shippingAddress1' => '123 Shipping St',
            'shippingAddress2' => 'Shipsville',
            'shippingCity'     => 'Shipstown',
            'shippingPostcode' => '54321',
            'shippingState'    => 'NY',
            'shippingCountry'  => 'US',
            'shippingPhone'    => '(555) 987-6543'
        );
    }

    protected function getIntlCurrencySymbol() {
        if (is_null($this->intlCurrencySymbol)) {
            $defaultLocale = \Locale::getDefault(); // REQUIRES extension=php_intl.dll TURNED ON
            $formatter = new \NumberFormatter($defaultLocale, \NumberFormatter::CURRENCY);
            $this->intlCurrencySymbol = $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }
        return $this->intlCurrencySymbol;
    }












    // /**
    //  * An associative array used for storing instantiated ClubSpeed payment classes.
    //  */
    // private $_lazy;

    // /**
    //  * A pointer to the CSLogic container class.
    //  */
    // private $logic;
    
    // public function __construct(&$CSLogic) {
    //     $this->_lazy = array();
    //     $this->logic = $CSLogic;
    // }

    // // this structure works fine, but requires us to call functions instead of referencing properties
    // // this may be bad practice (!!!) make sure we test this --
    // // if all else fails, bypass the __get, and call the methods externally instead of the properties
    // function __get($prop) {
    //     switch($prop) {
    //         case 'payPalExpress': return $this->payPalExpress();
    //         case 'sagePayDirect': return $this->sagePayDirect();
    //         case 'sagePayServer': return $this->sagePayServer();
    //         default:              throw new \CSException("Attempted to access an invalid CSLogic subclass! Received: " . $prop);
    //     }
    // }

    // /**
    //  * A lazy-loading reference to a instantiated CSPaymentsSagePayDirect class
    //  * which contains database interface methods for payments using Sage Pay Direct.
    //  */
    // public function payPalExpress() {
    //     if (!isset($this->_lazy['payPalExpress'])) {
    //         require_once(__DIR__.'/CSPaymentsPayPalExpress.php');
    //         $this->_lazy['payPalExpress'] = new \ClubSpeed\Payments\CSPaymentsPayPalExpress($this, $this->logic);
    //     }
    //     return $this->_lazy['payPalExpress'];
    // }

    // /**
    //  * A lazy-loading reference to a instantiated CSPaymentsSagePayDirect class
    //  * which contains database interface methods for payments using Sage Pay Direct.
    //  */
    // public function sagePayDirect() {
    //     if (!isset($this->_lazy['sagePayDirect'])) {
    //         require_once(__DIR__.'/CSPaymentsSagePayDirect.php');
    //         $this->_lazy['sagePayDirect'] = new \ClubSpeed\Payments\CSPaymentsSagePayDirect($this, $this->logic);
    //     }
    //     return $this->_lazy['sagePayDirect'];
    // }

    // /**
    //  * A lazy-loading reference to a instantiated CSPaymentsSagePayServer class
    //  * which contains database interface methods for payments using Sage Pay Server.
    //  */
    // public function sagePayServer() {
    //     if (!isset($this->_lazy['sagePayServer'])) {
    //         require_once(__DIR__.'/CSPaymentsSagePayServer.php');
    //         $this->_lazy['sagePayServer'] = new \ClubSpeed\Payments\CSPaymentsSagePayServer($this, $this->logic);
    //     }
    //     return $this->_lazy['sagePayServer'];
    // }
}