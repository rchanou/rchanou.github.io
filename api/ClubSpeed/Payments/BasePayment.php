<?php

namespace ClubSpeed\Payments;
use Omnipay\Omnipay;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Templates\TemplateService as Templates;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Payments\ProductHandlers\ProductHandlerService as Handlers;
use ClubSpeed\Utility\Tokens;
use ClubSpeed\Database\Helpers\UnitOfWork;

class BasePayment {

    protected $logic;
    protected $db;
    protected $handlers;
    protected $paymentService;
    protected $gateway;
    protected $intlCurrencySymbol;
    protected $logPrefix;
    protected $webapi;

    protected $currency;
    protected $formatter;
    protected $locale;

    public function __construct(&$logic, &$db, &$paymentService) {
        $this->logic = $logic;
        $this->db = $db;
        $this->paymentService = $paymentService;
    }

    protected function init($data) {
        if (!isset($data['name']) || empty($data['name'])) {
            $message = "Attempted to process a payment without providing a payment processor name!";
            Log::error($message, Enums::NSP_BOOKING);
            throw new \CSException($message);
        }
        $name = $data['name'];
        $available = $this->paymentService->available();
        $allowed = Arrays::contains($available, function($processor) use ($name) {
            return (isset($processor['name']) && $processor['name'] === $name);
        });
        if (!$allowed) {
            $message = "Attempted to process a payment by using an unsupported payment processor! Received: " . $name;
            Log::error($message, Enums::NSP_BOOKING);
            throw new \CSException($message);
        }
        $this->gateway = Omnipay::create(@$data['name']);
        $this->gateway->initialize(@$data['options'] ?: array());
        $this->logPrefix = 'Check #' . @$data['check']['checkId'] . ": Base: ";
        Log::info($this->logPrefix . "Starting payment using processor: " . $name, Enums::NSP_BOOKING);

        // load all defaults
        try {
            $locale = $this->logic->controlPanel->get('Booking', 'numberFormattingLocale');
            $locale = $locale[0];
            $locale = $locale->SettingValue ?: \Locale::getDefault(); // safe?
        }
        catch(\Exception $e) {
            $locale = \Locale::getDefault(); // and if this fails?
        }
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        try {
            $currency = $this->logic->controlPanel->get('Booking', 'currency');
            $currency = $currency[0];
            $currency = $currency->SettingValue ?: $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }
        catch (\Exception $e) {
            $currency = $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
        }

        $this->locale = $locale;
        $this->formatter = $formatter;
        $this->currency = $currency;
    }

    protected function passthroughWrapper($params, $callback) {
        // assume params need to be send directly through gateway->completeRequest without being modified
    }

    private function refresh($checkId) {
        // note: this is inefficient, but safe.
        // all calculations will be done in the database side.
        // ineffiency comes in when we have to get, and re-get, and re-get the check totals record.
        // will optimize if necessary, leaving for now
        $checkTotals = $this->logic->checkTotals->get($checkId);
        return $checkTotals[0];
    }

    protected function wrapper($params, $callback) {
        $this->init($params); // initialize omnipay library/driver 
        $checkId = Convert::toNumber(@$params['check']['checkId']);
        if (!isset($checkId) || is_null($checkId) || !is_int($checkId))
            throw new \RequiredArgumentMissingException("Payment processor received an invalid format for checkId! Received: " . @$params['check']['checkId']);
        $check = $this->logic->checks->get($checkId);
        if (!isset($check) || is_null($check) || empty($check))
            throw new \InvalidArgumentValueException("Payment processor received a checkId which could not be found in the database! Received: " . $checkId);
        $check = $check[0];
        if ($check->CheckStatus != 0)
            throw new \InvalidArgumentValueException("Payment processor received a checkId with a status other than 0 (open)! Found Check.Status: " . $check->CheckStatus);
        $this->logic->checks->applyCheckTotal($checkId); // ensure that the checktotal is stored on the checks record, for backwards compatibility
        $check = $this->logic->checks->get($checkId);
        $check = $check[0]; // re-get the check record after the applyCheckTotal stored procedure is called
        $checkTotals = $this->refresh($checkId);
        $response = null;

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
        
        if ($checkTotals->CheckRemainingTotal <= 0)
            throw new \CSException("Check " . $checkId . " has a balance of: " . $checkTotals->CheckRemainingTotal);
        
        /************************************\
         *** gift card processing section ***
        \************************************/
        if (isset($params['giftCards'])) {
            $giftCardIds = $params['giftCards'];
            $this->applyGiftCards($checkTotals->CheckID, $giftCardIds);
            $checkTotals = $this->refresh($checkId);
        }

        /**************************************\
         *** credit card processing section ***
        \**************************************/
        if ($checkTotals->CheckRemainingTotal == 0) {
            // gift cards have fully paid off the balance -- move on to handleSuccess
            $params['checkSnapshot'] = clone $checkTotals; // take the snapshot now (?)
            return $this->finalizeCheck($checkId, $params); // bypass handleSuccess and hit finalizeCheck
        }
        else {
            if (!isset($params['card'])) {
                // if the credit card information is not set, then error! there is a balance still to be paid, and no payment is available!
                Log::error($this->logPrefix . "Customer " . $checkTotals->CustID . " attempted to pay for check using only gift cards, but the gift cards point balances were too low! Card IDs: " . print_r($giftCardIds, true), Enums::NSP_BOOKING);
                // send off to handle failure -- no need to throw an exception, but how do we use the message?
                return $this->handleFailure($checkId, "Check balance cannot be fully covered by the provided payment items!");
            }
            else {
                try {
                    // pay the remaining total using omnipay
                    $options = array(
                        'amount'                 => $checkTotals->CheckRemainingTotal // note that this INCLUDES the tax -- the only reason tax is included below is for PCCharge
                        , 'currency'             => $this->currency
                        , 'description'          => "ClubSpeed payment for CheckID: " . $check->CheckID // build description manually?
                        , 'transactionId'        => $check->CheckID
                        , 'transactionReference' => 'some_transaction_reference' // use session id from laravel?
                        , 'cardReference'        => 'some_card_reference'
                        , 'taxAmount'            => $checkTotals->CheckRemainingTax // this is for WebAPI remoting interface, PCCharge requires it
                        , 'returnUrl'            => $this->getReturnUrl()
                        , 'cancelUrl'            => $this->getCancelUrl()
                        , 'notifyUrl'            => $this->getNotifyUrl()
                        , 'issuer'               => ''
                        , 'card'                 => new \Omnipay\Common\CreditCard(@$params['card'])
                        , 'clientIp'             => $this->getIp() // use the api ip? or the client ip? or the middle-tier ip?
                        , 'session'              => Tokens::generate()
                    );
                    Log::info($this->logPrefix . 'Charging card for ' . $options['amount'] . ' ' . $options['currency']);
                    $response = $callback($options); // the actual omnipay call
                }
                catch(\Exception $e) {
                    // note that sometimes omnipay can throw exceptions, so we need to catch and handle those,
                    // as well as checking for a non-successful, non-redirect response which will not throw exceptions.
                    // for example, an invalid card number structure
                    return $this->handleFailure($check->CheckID, $e->getMessage());
                }

                if (isset($response) && !empty($response)) {
                    if ($response->isSuccessful())
                        return $this->handleSuccess($check->CheckID, $params, $response);
                    else if ($response->isRedirect())
                        return $this->handleRedirect($check, $response);
                    else
                        return $this->handleFailure($check->CheckID, $response->getMessage());
                }
            }
        }
    }

    public function applyGiftCards($checkId, $giftCardIds = array()) {
        $checkTotals = $this->refresh($checkId);
        $overallTaxPercent = $checkTotals->CheckRemainingTax / $checkTotals->CheckRemainingTotal; // note -- this is the percentage of tax for the check as a whole, regardless of the individual check details' tax rates
        foreach($giftCardIds as $giftCardId) {
            try {
                if ($checkTotals->CheckRemainingTotal > 0) {
                    // we still have a balance to pay -- keep processing provided gift cards
                    $giftCardBalance = $this->logic->giftCardBalance->find('CrdID = ' . $giftCardId);
                    if (empty($giftCardBalance)) {
                        Log::error($this->logPrefix . "Customer " . $checkTotals->CustID . " attempted to use gift card #" . $giftCardId . " but it could not be found in the gift card history sums view!", Enums::NSP_BOOKING);
                        throw new \RecordNotFoundException("Unable to find a gift card history sum with a CrdID of " . $giftCardId);
                    }
                    $giftCardBalance = $giftCardBalance[0];
                    if ($giftCardBalance->Balance <= 0) {
                        Log::warn($this->logPrefix . "Customer " . $checkTotals->CustID . " attempted to use gift card #" . $giftCardId . " which has a balance of " . $giftCardBalance->Balance . "!", Enums::NSP_BOOKING);
                        // throw exception for trying to use a gift card which doesn't have points on it, or allow it?
                        continue; // or just continue through the loop of card ids
                    }

                    // start building the payment record
                    $giftCardPayment              = $this->logic->payment->dummy();
                    $giftCardPayment->CheckID     = $checkTotals->CheckID;
                    $giftCardPayment->PayDate     = Convert::getDate();
                    $giftCardPayment->PayStatus   = Enums::PAY_STATUS_PAID;
                    $giftCardPayment->PayTerminal = 'api';
                    $giftCardPayment->PayType     = Enums::PAY_TYPE_GIFT_CARD;
                    $giftCardPayment->UserID      = 0; // support id for now?
                    $giftCardPayment->CustID      = $giftCardBalance->CustID; // gift card payment expects CustID to be the CustID from the Gift Card customer record (and will be used as a later lookup)

                    // start building the gift card history record
                    $giftCardHistory                = $this->logic->giftCardHistory->dummy();
                    $giftCardHistory->CheckDetailID = $checkTotals->CheckDetailID;
                    $giftCardHistory->CheckID       = $checkTotals->CheckID;
                    $giftCardHistory->CustID        = $giftCardBalance->CustID; // DONT use check's CustID -- we need the CustID for the gift card
                    $giftCardHistory->Type          = 10; // GiftCardHistoryType.PayByGiftCard = 10
                    $giftCardHistory->UserID        = 0; // support id for now?

                    if ($giftCardBalance->Balance >= $checkTotals->CheckRemainingTotal) {
                        // this gift card can pay off the outstanding balance

                        $giftCardPayment->PayAmount = $checkTotals->CheckRemainingTotal; // this is expected to be the total, NOT the subtotal (tax included in this number)
                        $giftCardPayment->PayTax = $checkTotals->CheckRemainingTax;

                        $giftCardHistory->Points = -1 * $checkTotals->CheckRemainingTotal; // decrement the remaining total

                        // all remaining totals/taxes will be accounted for when these payments are processed
                        // $checkRemaining->CheckTotal = 0; // or should we change it by the TotalToBeApplied in the case of math errors so we can keep track of where they went wrong?
                        // $checkRemaining->CheckSubtotal = 0;
                        // $checkRemaining->CheckTax = 0;
                        // note that we could also modify PaidAmount and PaidTax, now that they are CheckTotals_V fields
                        // is this a better option??
                    }
                    else {
                        // this card can only partially cover the outstanding balance

                        $cardTotalToBeApplied = $giftCardBalance->Balance;
                        $cardTaxToBeApplied = round($cardTotalToBeApplied * $overallTaxPercent, 2); // round to nearest 2 decimals -- sufficient for a partial payment(??)
                        $cardSubtotalToBeApplied = $cardTotalToBeApplied - $cardTaxToBeApplied; // what about VAT? this isn't actually used right now

                        $giftCardPayment->PayAmount = $cardTotalToBeApplied; // again, this is expected to be the representation of the total (!!!) (tax included)
                        $giftCardPayment->PayTax = $cardTaxToBeApplied;

                        $giftCardHistory->Points = -1 * $cardTotalToBeApplied; // remove remaining points from this gift card
                    }

                    // create the payment and gift card history now
                    // in the event of failure, revert back to a standard state
                    $giftCardPaymentId = $this->logic->payment->create($giftCardPayment);
                    $giftCardPaymentId = $giftCardPaymentId[$giftCardPayment::$key];
                    $this->logic->giftCardHistory->create($giftCardHistory);
                    Log::info($this->logPrefix . "Applied payment #" . $giftCardPaymentId . " for " . $giftCardPayment->PayAmount . " from gift card #" . $giftCardBalance->CrdID, Enums::NSP_BOOKING);

                    // recollect checkTotals each time
                    // to allow database to handle the summations
                    // and payment calculations instead of checkRemaining (?)
                    // we could also modify PaidAmount and PaidTax in memory,
                    // then do one last get to verify PHP and DB calculations line up
                    // if performance is an issue with a large number of gift cards
                    $checkTotals = $this->refresh($checkId);
                }
            }
            catch (\Exception $e) {
                Log::error($this->logPrefix . "Unable to apply payment from Gift Card #" . $giftCardId, Enums::NSP_BOOKING, $e);
                $this->handleFailure($checkTotals->CheckID, $e->getMessage()); // what params? don't pass gift cards, we should look up any gift card payments in the database itself
            }
        }
        // return $remainingTotal;
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

        $this->init($params);

        // we need to have the transaction reference before sending
        

        $response = $this->gateway->completePurchase($params)->send();
        $checks = $this->logic->checks->match(array('Notes' => 'Transaction Reference: ' . $response->getTransactionReference())); // find the check with the notes that match the transactionReference
        $check = $checks[0];

        if ($response->isSuccessful())
            return $this->handleSuccess($check, $response);
        else if ($response->isRedirect())
            return $this->handleRedirect($check, $response); // this probably shouldn't happen
        else
            return $this->handleFailure($check->CheckID, $response->getMessage());
    }

    public function handleSuccess($checkId, &$params, &$response = null) {

        Log::info($this->logPrefix . "Payment was fully collected from Omnipay processor", Enums::NSP_BOOKING);
        // the customer's credit card has been processed at this point
        // or the credit card does not need to be processed (?) (as in, gift cards provided > than total required)

        // better way to collect the payment amount? this doesn't seem reliable.
        // need to use dbo.TransactionReferences moving forward
        $responseData = $response->getData();
        $params['transactionReference'] = $response ? $response->getTransactionReference() : Enums::DB_NULL;
        $now = Convert::getDate();

        try {
            $checkTotals = $this->logic->checkTotals->match(array('CheckID' => $checkId));
            $checkTotal = $checkTotals[0];

            if ($checkTotal->CheckRemainingTotal > 0) { // sanity check
                // build and insert a payment record

                // we need to keep track of what needs to be paid BEFORE it is paid, for use with the receipt email
                // bad, but works. leaving for now.
                $params['checkSnapshot'] = clone $checkTotal;
                // $params['finalCheckPayment'] = $checkTotal->CheckRemainingTotal;

                $payment                        = $this->logic->payment->dummy();
                $payment->CustID                = $checkTotal->CustID;
                $payment->CheckID               = $checkTotal->CheckID;
                $payment->ExtCardType           = $params['name'];
                // $payment->PayAmount             = $amount; // $amount comes from the response, other option would be $checkTotal->CheckRemainingTotal, which is what was sent with the request
                $payment->PayAmount             = $checkTotal->CheckRemainingTotal;
                $payment->PayDate               = $now;
                $payment->PayStatus             = Enums::PAY_STATUS_PAID;
                $payment->PayTax                = $checkTotal->CheckRemainingTax;
                $payment->PayTerminal           = 'api';// use this?
                $payment->PayType               = Enums::PAY_TYPE_EXTERNAL;
                $payment->TransactionDate       = $now;
                $payment->TransactionReference  = $params['transactionReference'];
                $payment->ExternalAccountNumber = ''; // front end logs error if this is null
                $payment->ExternalAccountName   = ''; // front end logs error if this is null
                $payment->UserID                = 1; // probably should be non-nullable, onlinebooking userId?

                $uow = UnitOfWork::build()
                    ->action('create')
                    ->data((array)$payment);
                $this->logic->payment->uow($uow);
                $paymentId = $uow->table_id; // some day we'll get to gank the root logic/db functions and use them to abstract away direct UoW creation..
                Log::info($this->logPrefix . "Applied payment of " . $payment->PayAmount . " at Payment #" . $paymentId, Enums::NSP_BOOKING);
            }
            else {
                Log::warn($this->logPrefix . "Attempted to apply payment, but there was nothing left to be paid!", Enums::NSP_BOOKING); // todo: extend error properly
                // is this an error? the check supposedly doesn't have anything left to be paid, but we are still trying to apply a payment
            }
        }
        catch (\Exception $e) {
            Log::error($this->logPrefix . 'Omnipay has registered a successful payment but we were unable to apply the payment to the Payments table! Transaction reference was: ' . $params['transactionReference'], Enums::NSP_BOOKING, $e);
            // continue at this point?
            // if we continue, the check will be closed,
            // all line items will be processed (gift cards, etc),
            // and receipt email will be sent
        }
        return $this->finalizeCheck($checkId, $params); // should this be below the catch, or at the end of the try?
    }

    public function finalizeCheck($checkId, $params) {
        $metadata = (isset($params['check']) ? $params['check'] : array());
        if (isset($params['sendCustomerReceiptEmail']))
            $metadata['sendCustomerReceiptEmail'] = $params['sendCustomerReceiptEmail']; // gross.
        try {
            $receiptData = $this->logic->checks->finalize($checkId, $metadata); // hacky
            return $receiptData;
        }
        catch(\Exception $e) {
            // at this point, the payment is already taken and saved,
            // but the check was unable to finalize.

            // how should we handle these exceptions? support email? track email?
            // exception should already be logged by checks->finalize() and rethrown back to here.
        }
    }

    public function handleRedirect($check, $response) {
        // TODO!!!!
        Log::info("inside handle redirect!!!", Enums::NSP_BOOKING);

        // we need to store the majority of this information by transaction ... id? reference?
        // one of the two. so we can look it up again later, once the post comes back from the booking front end.

        $data = $response->getData();

        return array(
            'redirect' => array(
                'url'       => $response->getRedirectUrl(),
                'method'    => $response->getRedirectMethod(),
                'data'      => $response->getRedirectData()
                // 'reference' => $response->getTransactionReference() // client probably doesn't need this info, and will most likely get it from the payment processor (?)
            )
        );
    }

    public function handleFailure($checkId, $message = null) {
        // what will $message be here? a response? an exception? a string? who knows?
        // this function should only be hit before a user's credit card is charged

        // revert any attempted gift card purchases
        // by voiding any gift card payments
        // and applying the reverse to the gift card history

        // is there ever a situation where a check will already have a gift card applied to the payments
        // and we run the risk of accidentally reverting this gift card payment?
        // worst case scenario, the gift card will just have the balance back on it

        Log::error($this->logPrefix . "Unable to finish processing check! Message: " . $message, Enums::NSP_BOOKING);
        $checkTotals = $this->refresh($checkId);

        $checkDetails = array();
        try {
            $checkDetails = $this->logic->checkDetails->match(array(
                'CheckID' => $checkId
            ));
            Log::info($this->logPrefix . "Check Detail Count: " . count($checkDetails), Enums::NSP_BOOKING);
        }
        catch(\Exception $e) {
            Log::error($this->logPrefix . "Unable to find any check details by CheckID!", Enums::NSP_BOOKING, $e);
        }
        foreach($checkDetails as $checkDetail) {
            try {
                $this->logic->checkDetails->update($checkDetail->CheckDetailID, array(
                    'Status' => Enums::CHECK_DETAIL_STATUS_HAS_VOIDED,
                    'VoidNotes' => $this->logPrefix . 'Voiding from purchase error: ' . $message
                ));
                Log::info($this->logPrefix . "Voided CheckDetail #" . $checkDetail->CheckDetailID, Enums::NSP_BOOKING);
            }
            catch(\Exception $e) {
                Log::error($this->logPrefix . "Unable to void CheckDetail #" . $checkDetail->CheckDetailID, Enums::NSP_BOOKING, $e);
            }
        }
        try {
            $this->logic->checks->update($checkId, array(
                'CheckStatus' => Enums::CHECK_STATUS_CLOSED,
                'ClosedDate'  => Convert::getDate()
            ));
            Log::info($this->logPrefix . 'Closed check after failed payment!', Enums::NSP_BOOKING);
        }
        catch (\Exception $e) {
            Log::error($this->logPrefix . "Unable to close check after failed payment!", Enums::NSP_BOOKING, $e);
        }
        try {
            $this->logic->checks->applyCheckTotal($checkId);
            Log::info($this->logPrefix . 'Called dbo.ApplyCheckTotal', Enums::NSP_BOOKING);
        }
        catch (\Exception $e) {
            Log::error($this->logPrefix . 'Unable to call dbo.ApplyChecktotal!', Enums::NSP_BOOKING, $e);
        }

        // assume that the check will be re-created, not re-used on failure

        // find all check details
        // void all check details (cd.Status = 2)
        // close the check record
        // apply check totals

        $giftCardPayments = array();
        try {
            $giftCardPayments = $this->logic->payment->match(array(
                'CheckID' => $checkTotals->CheckID
                , 'PayType' => Enums::PAY_TYPE_GIFT_CARD
            ));
            Log::info($this->logPrefix . "Gift card payment count: " . count($giftCardPayments), Enums::NSP_BOOKING);
        }
        catch(\Exception $e) {
            Log::error($this->logPrefix . "Unable to search for gift card payments!", Enums::NSP_BOOKING, $e);            
        }
        foreach($giftCardPayments as $giftCardPayment) {
            try {
                $giftCardBalance = $this->logic->giftCardBalance->match(array(
                    'CustID' => $giftCardPayment->CustID
                ));
                $giftCardBalance = $giftCardBalance[0];
                $giftCardPayment->PayStatus = Enums::PAY_STATUS_VOID;
                $this->logic->payment->update($giftCardPayment->PayID, $giftCardPayment);
                Log::info($this->logPrefix . "Voided Payment #" . $giftCardPayment->PayID . " for gift card #" . $giftCardBalance->CrdID, Enums::NSP_BOOKING);
            }
            catch (\Exception $e) {
                Log::error($this->logPrefix . "Unable to void Payment #" . $giftCardPayment->PayID . " for gift card #" . $giftCardBalance->CrdID, Enums::NSP_BOOKING, $e);
            }
            try {
                $giftCardHistory                = $this->logic->giftCardHistory->dummy();
                // $giftCardHistory->CheckDetailID = $checkTotals->CheckDetailID; // this isn't actually available (!!!) do we have a way to map payment back to the original CheckDetailID? This was the CheckDetailID of the original gift card purchase ??
                $giftCardHistory->CheckID       = $checkTotals->CheckID;
                $giftCardHistory->CustID        = $giftCardBalance->CustID; // DONT use check's CustID -- we need the CustID for the gift card
                $giftCardHistory->Type          = Enums::GIFT_CARD_HISTORY_REFUND_TO_GIFT_CARD; // GiftCardHistoryType.PayByGiftCard = 10
                $giftCardHistory->UserID        = 0; // support id for now?
                $giftCardHistory->Points        = $giftCardPayment->PayAmount; // refund the gift card
                $this->logic->giftCardHistory->create($giftCardHistory);
                Log::info($this->logPrefix . "Refunded " . $giftCardPayment->PayAmount . " to Gift Card#" . $giftCardBalance->CrdID . " from check error", Enums::NSP_BOOKING);
            }
            catch (\Exception $e) {
                Log::error($this->logPrefix . "Unable to refund " . $giftCardPayment->PayAmount . " to Gift Card#" . $giftCardBalance->CrdID . " for original check error", Enums::NSP_BOOKING, $e);
            }
        }

        throw new \CSException($message); // or return data with a 200? client probably expects an array of data

        // return array(
        //     'error' => array(
        //         'message' => isset($response) ? $response->getMessage() : "Unable to process payment!" // todo: better message
        //         , 'data' => isset($response) ? $response->getData() : array() // $response may or may not be null
        //     )
        // );
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

    protected function getAsCurrency($number) {
        // $this->formatter->format($number); // this also works
        return $this->formatter->formatCurrency($number, $this->currency);
    }
}