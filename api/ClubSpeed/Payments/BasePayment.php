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

        // what should the order of events be?
        // 1. get check total
        // 2. check to see if gift cards exist
        // 3. find the gift card balances
        // 4. if balances < check total and no credit card provided, throw error!
        // 5. calculate how much tax/subtotal should be applied to each gift card
        // 6. do we actually make the payment records, before the card is processed? we pretty much have to, in the case of redirect
        //      and if we make the payment records with gift cards and the credit card fails, do we delete those payment records and revert the gift card history records?
        //      what happens with the redirect payment with callback url?

        // for the purpose of testing, coding as follows:
        // - calculate gift card items, without saving payment records
        // - send off the payment attempt (ignore the server/offsite payments for now)
        // - if payment attempt is successful, finalize the gift card payment records
        // - alter gift card history after the payment records have been finalized

        // declare remaining items here -- they should be used with the omnipay payment call, if necessary
        $remainingTotal = $checkTotals->CheckTotal; // consider using CheckTotal - (PaidAmount + PaidTax) in the future, to take into account partially paid checks
        $remainingTax = $checkTotals->CheckTax;
        $remainingSubtotal = $checkTotals->CheckSubtotal;
        $overallTaxPercent = $remainingTax / $remainingTotal; // is this safe? rounding issues? note that some items on the check may be taxed differently and/or not taxed at all

        $virtuals = array();

        // handle gift cards here(???)
        // note that we need to handle the gift cards early
        // in order to ensure that we have the correct amount
        // being sent through omnipay, and not any more
        if (isset($params['giftCards'])) {
            $giftCardIds = $params['giftCards'];
            $virtuals['payment'] = array();
            $virtuals['giftCardHistory'] = array();
            foreach($giftCardIds as $giftCardId) {
                if ($remainingTotal > 0) {
                    // we still have a total to pay -- keep processing provided gift cards
                    $giftCardHistorySums = $this->logic->giftCardHistorySums->find('CrdID = ' . $giftCardId);
                    if (empty($giftCardHistorySums)) {
                        Log::error("Customer " . $checkTotals->CustID . " attempted to use gift card #" . $giftCardId . " but it could not be found in the gift card history sums view!");
                        throw new \RecordNotFoundException("Unable to find a gift card history sum with a CrdID of " . $giftCardId);
                    }
                    $giftCardHistorySums = $giftCardHistorySums[0];
                    if ($giftCardHistorySums->PointSum <= 0) {
                        // throw exception for trying to use a gift card which doesn't have points on it, or allow it?
                        continue; // or just continue through the loop of card ids
                    }

                    // start building the payment record
                    $giftCardPayment              = $this->logic->payment->dummy();
                    $giftCardPayment->CheckID     = $checkTotals->CheckID;
                    $giftCardPayment->PayDate     = \ClubSpeed\Utility\Convert::getDate();
                    $giftCardPayment->PayStatus   = 1; // PayStatus.PAID = 1
                    $giftCardPayment->PayTerminal = 'api';
                    $giftCardPayment->PayType     = 4; // PayType.GiftcardPayment = 4
                    $giftCardPayment->UserID      = 0; // support id for now?

                    // start building the gift card history record
                    $giftCardHistory                = $this->logic->giftCardHistory->dummy();
                    $giftCardHistory->CheckDetailID = $checkTotals->CheckDetailID;
                    $giftCardHistory->CheckID       = $checkTotals->CheckID;
                    $giftCardHistory->CustID        = $giftCardHistorySums->CustID; // DONT use check's CustID -- we need the CustID for the gift card
                    $giftCardHistory->Type          = 10; // GiftCardHistoryType.PayByGiftCard = 10
                    $giftCardHistory->UserID        = 0; // support id for now?

                    if ($giftCardHistorySums->PointSum >= $remainingTotal) {
                        // this gift card can pay off the outstanding balance

                        $giftCardPayment->PayAmount = $remainingTotal; // this is expected to be the total, NOT the subtotal (tax included in this number)
                        $giftCardPayment->PayTax = $remainingTax;

                        $giftCardHistory->Points = -1 * $remainingTotal; // decrement the remaining total

                        // all remaining totals/taxes will be accounted for when these payments are processed
                        $remainingTotal = 0;
                        $remainingSubtotal = 0;
                        $remainingTax = 0;
                    }
                    else {
                        // this card can only partially cover the outstanding balance

                        $cardTotalToBeApplied = $giftCardHistorySums->PointSum;
                        $cardTaxToBeApplied = round($cardTotalToBeApplied * $overallTaxPercent, 2); // round to nearest 2 decimals -- sufficient for a partial payment(??)
                        $cardSubtotalToBeApplied = $cardTotalToBeApplied - $cardTaxToBeApplied; // what about VAT?

                        $giftCardPayment->PayAmount = $cardTotalToBeApplied; // again, this is expected to be the representation of the total (!!!) (tax included)
                        $giftCardPayment->PayTax = $cardTaxToBeApplied;

                        $giftCardHistory->Points = -1 * $cardTotalToBeApplied; // remove remaining points from this gift card
                    
                        $remainingTotal -= $cardTotalToBeApplied;
                        $remainingSubtotal -= $cardSubtotalToBeApplied;
                        $remainingTax -= $cardTaxToBeApplied;
                    }

                    // store the virtual records to be created at a later time, after the credit card has been processed
                    $virtuals['payment'][] = $giftCardPayment;
                    $virtuals['giftCardHistory'][] = $giftCardHistory;
                }
            }

            pr($remainingTotal);
            pr($remainingSubtotal);
            pr($remainingTax);
            // pr($virtuals);

            // if the entire balance is covered by gift cards,
            // don't attempt to charge the card for the remaining balance (assuming its given)
            if ($remainingTotal > 0 && !isset($params['card'])) {
                Log::error("Customer " . $checkTotals->CustID . " attempted to pay for CheckID " . $check->CheckID . " using only gift cards, but the gift cards point balances were too low! Card IDs: " . print_r($giftCardIds, true));
                throw new \CSException("Gift card balance could not cover the outstanding check balance!");
            }
        }

        if ($remainingTotal === 0) {
            // gift cards have paid off the balance -- can we just move on to handleSuccess?
            return $this->handleSuccess($check, $params, $virtuals);
        }
        else {
            // pay the remaining total using omnipay
            $options = array(
                'amount'                 => $remainingTotal // note that this INCLUDES the tax -- the only reason tax is included below is for PCCharge
                , 'currency'             => $this->getIntlCurrencySymbol() // THIS REQUIRES php_intl.dll EXTENSION TURNED ON
                , 'description'          => "ClubSpeed payment for CheckID: " . $check->CheckID // build description manually?
                , 'transactionId'        => $check->CheckID
                , 'transactionReference' => 'some_transaction_reference' // use session id from laravel?
                , 'cardReference'        => 'some_card_reference'
                , 'taxAmount'            => $remainingTax // this is for WebAPI remoting interface, PCCharge requires it
                , 'returnUrl'            => $this->getReturnUrl()
                , 'cancelUrl'            => $this->getCancelUrl()
                , 'notifyUrl'            => $this->getNotifyUrl()
                , 'issuer'               => ''
                , 'card'                 => new \Omnipay\Common\CreditCard(@$params['card'])
                , 'clientIp'             => $this->getIp() // use the api ip? or the client ip? or the middle-tier ip?
            );

            $response = $callback($options);

            if ($response->isSuccessful())
                return $this->handleSuccess($check, $params, $virtuals, $response);
            else if ($response->isRedirect())
                return $this->handleRedirect($check, $response);
            else
                return $this->handleFailure($check, $response);
        }
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

    public function handleSuccess(&$check, &$params, &$virtuals, &$response = null) {
        // the customer's credit card has been processed at this point
        // or the credit card does not need to be processed (?) (as in, gift cards provided > than total required)

        $transactionReference = $response ? $response->getTransactionReference() : "No external reference: See CheckID";

        $now = \ClubSpeed\Utility\Convert::getDate();
        $handled = array();
        $errored = array();

        $checkData = @$params['check'];

        $checkTotals = $this->logic->checkTotals->match(array('CheckID' => $check->CheckID)); // this wont work with gift cards
        $checkTotal = $checkTotals[0];

        // build and insert a payment record
        $payment                  = $this->logic->payment->dummy();
        $payment->CheckID         = $check->CheckID;
        $payment->PayAmount       = $check->CheckTotal; // also won't work with gift cards
        $payment->PayDate         = $now;
        $payment->PayStatus       = 1; // PayStatus.PAID from VB
        $payment->PayTax          = $checkTotal->CheckTax; // also won't work with gift cards
        $payment->PayTerminal     = 'api';// use this?
        $payment->PayType         = 2; // always credit card when through pccharge?
        $payment->TransactionDate = $now;
        $payment->ReferenceNumber = $transactionReference;
        $payment->UserID          = 1; // probably should be non-nullable, onlinebooking userId?
        $this->logic->payment->create($payment); // what if this fails? credit card will be charged, but payment record could not be created (!!!)

        // run all of the virtuals -- these will most likely be gift card payments not added to the database yet
        // note -- this idea will most likely not work when we support external payment processors with redirects
        foreach($virtuals as $key => $virtual) {
            foreach($virtual as $record) {
                $this->logic->{$key}->create($record);                
            }
        }

        // consider the check to be closed at this point --
        // once all payments have been added to the database
        // update the check
        $check->CheckStatus = 1; // CheckStatus.Closed from VB enum
        $check->Notes = $transactionReference;
        $check->ClosedDate = $now;
        $this->logic->checks->update($check->CheckID, $check);

        foreach($checkTotals as $checkTotal) {
            $metadata = \ClubSpeed\Utility\Arrays::first($checkData['details'], function($val, $key, $arr) use ($checkTotal) {
                return isset($val['checkDetailId']) && $val['checkDetailId'] == $checkTotal->CheckDetailID;
            });
            try {
                // who handles the for loop for quantity? this, or the handler?
                // note that these should really not be Qty, but should be their own CheckDetail records items
                // handling here to represent an easier update if Qty ever gets deprecated
                for ($i = 0; $i < $checkTotal->Qty; $i++) {
                    $handle = $this->handlers->handle($checkTotal, $metadata);
                    if (isset($handle['error']))
                        $errored[] = $handle;
                    else
                        $handled[] = $handle;
                }
            }
            catch (\Exception $e) {
                $errored[] = $e->getMessage();
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
            ->body("This is my email body for now! Woohoo! Receipt template TODO!" . print_r($handled, true));
        try {
            Mail::send($mail);
            Log::debug("Receipt email for CheckID " . $check->CheckID . " has been sent to: " . $customer->EmailAddress);
        }
        catch(\Exception $e) {
            Log::error("Receipt email for CheckID " . $check->CheckID . " could not be sent to: " . $customer->EmailAddress, $e);
        }

        if (!empty($errored)) {
            pr("found errors!");
            die(print_r($errored));
            // TODO -- send off a support email if product handlers had errors?
        }
    }

    public function handleRedirect($check, $response) {
        // TODO!!!!
        return array(
            'redirect' => array(
                'url'       => $response->getRedirectUrl(),
                'method'    => $response->getRedirectMethod(),
                'data'      => $response->getRedirectData()
                // 'reference' => $response->getTransactionReference() // client probably doesn't need this info, and will most likely get it from the payment processor (?)
            )
        );
    }

    public function handleFailure($check, $params, $response) {

        // void out all attempted gift card purchases?
        // if virtual, then we don't need to worry
        // if redirect, then we will have problems (fix later)
        // refund through gift card history?

        return array(
            'error' => array(
                'message' => $response->getMessage()
                , 'data' => $response->getData()
            )
        );
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