<?php

namespace ClubSpeed\Payments;

class CSPaymentsBase {

    protected $payments;
    protected $gateway;
    protected $logic;

    private $intlCurrencySymbol;

    public function __construct(&$CSPayments, &$CSLogic) {
        $this->payments = $CSPayments; // necessary? probably not
        $this->logic = $CSLogic;
    }

    // public abstract function handleResponse($response); // is this really the best way to handle this?
    // public abstract function handleRedirect($)

    public function handleSuccess($check, $response) {
        // do something!
        return;
    }

    public function handleRedirect($check, $response) {
        // re-get the check, since we need the Checks object, not the CheckTotals_V object
        // $check = $this->logic->checks->get($checkId);
        // $check = $check[0];
        // $check->Notes = $response->getTransactionReference();
        // $this->logic->checks->update($check->CheckID, $check);
        return array(
            'redirect' => array(
                'url'       => $response->getRedirectUrl(),
                'method'    => $response->getRedirectMethod(),
                'data'      => $response->getRedirectData(),
                // 'reference' => $response->getTransactionReference() // client probably doesn't need this info
            )
        );
    }

    public function handleFailure($check, $response) {
        pr($response->getMessage());
        pr($response->getData());
        die();
    }

    protected function passthroughWrapper($params, $callback) {
        // assume params need to be send directly through gateway->completeRequest without being modified

    }

    protected function wrapper($params, $callback) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $checkId = \ClubSpeed\Utility\Convert::toNumber(@$params['checkId']);
        if (!isset($checkId) || is_null($checkId) || !is_int($checkId))
            throw new \RequiredArgumentMissingException("Payment authorize received an invalid format for checkId! Received: " . @$params['checkId']);
        $checkTotals = $this->logic->checkTotals->get($checkId);
        if (!isset($checkTotals) || is_null($checkTotals) || empty($checkTotals))
            throw new \InvalidArgumentValueException("Payment authorize received a checkId which could not be found in the database! Received: " . $checkId);
        $checkTotals = $checkTotals[0];
        $this->logic->checks->applyCheckTotal($checkTotals->CheckID); // ensure that the checktotal is stored on the checks record, for backwards compatibility

        $options = array(
            'amount'                 => $checkTotals->CheckTotal // this is why we grabbed the CheckTotals_V object instead of the Checks object
            , 'currency'             => $this->getIntlCurrencySymbol() // THIS REQUIRES php_intl.dll EXTENSION TURNED ON
            , 'description'          => "ClubSpeed payment for CheckID: " . $checkTotals->CheckID // build description manually?
            , 'transactionId'        => $checkTotals->CheckID . ((int)rand())
            , 'transactionReference' => 'some_transaction_reference' // use session id from laravel?
            , 'cardReference'        => 'some_card_reference'
            , 'returnUrl'            => $this->getReturnUrl()
            , 'cancelUrl'            => $this->getCancelUrl()
            , 'notifyUrl'            => $this->getNotifyUrl()
            , 'issuer'               => ''
            , 'card'                 => new \Omnipay\Common\CreditCard($params)
            , 'clientIp'             => $this->getIp() // use the api ip? or the client ip?
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
        $response = $this->gateway->completePurchase($options)->send();
        $checks = $this->logic->checks->match(array('Notes' => $response->getTransactionReference()));
        $check = $checks[0];

        if ($response->isSuccessful())
            return $this->handleSuccess($check, $response);
        else if ($response->isRedirect())
            return $this->handleRedirect($check, $response);
        else
            return $this->handleFailure($check, $response);

        // return $this->wrapper($params, function($options) {
        //     return $this->gateway->completePurchase($options)->send(); // TEST THIS -- probably won't work, but try it
        // });
        // $response = $this->gateway->completePurchase($options)->send();

        // return $this->gateway->completePurchase($options)->send();
    }

    protected function getIp() {
        return getHostByName(php_uname('n'));
    }

    protected function getReturnUrl() {
        return 'http://' . $this->getIp() . "/api/index.php/payments/" . $this->namespace . "/completePurchase/"; // TODO: get actual return url
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
}