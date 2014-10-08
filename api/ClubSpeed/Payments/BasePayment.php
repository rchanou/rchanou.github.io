<?php

namespace ClubSpeed\Payments;
use Omnipay\Omnipay;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Logging\LogService as Log;

class BasePayment {

    protected $logic;
    protected $handlers;
    protected $gateway;
    protected $intlCurrencySymbol;

    public function __construct(&$logic, &$handlers) {
        $this->logic = $logic;
        $this->handlers = $handlers;
    }

    protected function init($data) {
        $this->gateway = Omnipay::create(@$data['name']);
        $this->gateway->initialize(@$data['options']);
    }

    public function handleSuccess(&$check, &$params, &$response) {
        $now = \ClubSpeed\Utility\Convert::getDate();

        $checkData = @$params['check'];

        // update the check
        $check->CheckStatus = 1; // CheckStatus.Closed from VB enum
        $check->Notes = $response->getTransactionReference();
        $check->ClosedDate = $now;
        $this->logic->checks->update($check->CheckID, $check);

        $checkTotals = $this->logic->checkTotals->match(array('CheckID' => $check->CheckID));
        $checkTotal = $checkTotals[0];

        // build and insert a payment record
        $payment                  = $this->logic->payment->dummy();
        $payment->CheckID         = $check->CheckID;
        $payment->PayAmount       = $check->CheckTotal;
        $payment->PayDate         = $now;
        $payment->PayStatus       = 1; // PayStatus.PAID from VB
        $payment->PayTax          = $checkTotal->CheckTax; // this will be the same on each record, as long as the CheckID matches
        $payment->PayTerminal     = 'api';// use this?
        $payment->PayType         = 2; // always credit card when through pccharge?
        $payment->TransactionDate = $now;
        $payment->ReferenceNumber = $response->getTransactionReference();
        $payment->UserID          = 1; // probably should be non-nullable, onlinebooking userId?
        $this->logic->payment->create($payment);

        // should probably have a try catch here for each check detail
        $handled = array();
        foreach($checkTotals as $checkTotal) {
            $metadata = \ClubSpeed\Utility\Arrays::first($checkData['details'], function($val, $key, $arr) use ($checkTotal) {
                return isset($val['checkDetailId']) && $val['checkDetailId'] == $checkTotal->CheckDetailID;
            });
            try {
                $handled[] = $this->handlers->handle($checkTotal, $metadata); // is this the data we are using to build the receipt?
            }
            catch (\Exception $e) {
                $handled[] = $e->getMessage();
                // should do something here -- part of the check was not able to be processed
            }
        }

        $businessName = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = BusinessName");
        $businessName = $businessName[0];
        $businessName = $businessName->SettingValue;

        $customer = $this->logic->customers->get($checkTotal->CustID);
        $customer = $customer[0];
        $emailTo  = array($customer->EmailAddress => $customer->FName . ' ' . $customer->LName);

        $emailFrom = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = EmailWelcomeFrom");
        $emailFrom = $emailFrom[0];
        $emailFrom = array($emailFrom->SettingValue => $businessName);

        $mail = Mail::builder()
            ->subject($businessName . ' Receipt for Order Number: ' . $check->CheckID)
            ->from($emailFrom)
            ->to($emailTo)
            ->body("This is my email body for now! Woohoo! Receipt template TODO!");
        try {
            Mail::send($mail);
            Log::debug("Receipt email for CheckID " . $check->CheckID . " has been sent to: " . $customer->EmailAddress);
        }
        catch(\Exception $e) {
            Log::error("Receipt email for CheckID " . $check->CheckID . " could not be sent to: " . $customer->EmailAddress, $e);
        }
        // need to send handled off to a mail service, or receipt service, or something
        // -- someone needs to send off an email to the customer before returning success
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
        $checkId = \ClubSpeed\Utility\Convert::toNumber(@$params['check']['checkId']);
        if (!isset($checkId) || is_null($checkId) || !is_int($checkId))
            throw new \RequiredArgumentMissingException("Payment processor received an invalid format for checkId! Received: " . @$params['checkId']);
        $check = $this->logic->checks->get($checkId);
        if (!isset($check) || is_null($check) || empty($check))
            throw new \InvalidArgumentValueException("Payment processor received a checkId which could not be found in the database! Received: " . $checkId);
        $check = $check[0];
        if ($check->CheckStatus != 0)
            throw new \InvalidArgumentValueException("Payment processor received a checkId with a status other than 0 (open)! Found Check.Status: " . $check->CheckStatus);
        $this->logic->checks->applyCheckTotal($checkId); // ensure that the checktotal is stored on the checks record, for backwards compatibility
        $check = $this->logic->checks->get($checkId);
        $check = $check[0]; // re-get the check record after the applyCheckTotal stored procedure is called
        $checkTotals = $this->logic->checkTotals->get($checkId);
        $checkTotals = $checkTotals[0];

        $options = array(
            'amount'                 => $check->CheckTotal // this is why we grabbed the CheckTotals_V object instead of the Checks object
            , 'currency'             => $this->getIntlCurrencySymbol() // THIS REQUIRES php_intl.dll EXTENSION TURNED ON
            , 'description'          => "ClubSpeed payment for CheckID: " . $check->CheckID // build description manually?
            , 'transactionId'        => $check->CheckID
            , 'transactionReference' => 'some_transaction_reference' // use session id from laravel?
            , 'cardReference'        => 'some_card_reference'
            , 'taxAmount'            => $checkTotals->CheckTax // this is for WebAPI remoting interface, PCCharge requires it
            , 'returnUrl'            => $this->getReturnUrl()
            , 'cancelUrl'            => $this->getCancelUrl()
            , 'notifyUrl'            => $this->getNotifyUrl()
            , 'issuer'               => ''
            , 'card'                 => new \Omnipay\Common\CreditCard(@$params['card'])
            , 'clientIp'             => $this->getIp() // use the api ip? or the client ip? or the middle-tier ip?
        );

        $response = $callback($options);

        // $check->Notes = $response->getTransactionReference(); // store the tx reference in Checks.Notes, since we don't have another field to use at this time
        // $this->logic->checks->update($check->CheckID, $check);

        if ($response->isSuccessful())
            return $this->handleSuccess($check, $params, $response);
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
}