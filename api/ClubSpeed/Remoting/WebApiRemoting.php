<?php

namespace ClubSpeed\Remoting;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;

// Grab httpful -- necessary with autoloader?
require_once(__DIR__.'../../../httpful/httpful.phar'); // ~ 6 ms

/**
 * The CSWebApi class which contains references to remote methods
 * on the .NET WebApi which need to be executed by the PHP api
 * for interfacing with the MainEngine.
 */
class WebApiRemoting {

    /**
     * The remote api info broken into an associative array.
     */
    private $apiInfo;

    /**
     * The remote api info imploded into a single base url.
     */
    private $apiBase;

    protected $logic;
    protected $db;

    /**
     * Creates a new instance of the CSWebApi class.
     */
    public function __construct(&$logic, &$db) {
        $this->logic = $logic;
        $this->db = $db;
        $this->apiInfo = array(
            // 'protocol'  => ($this->isSecure() ? 'https' : 'http').'://'
            'protocol'  => 'https://' // https is required for WebAPI -- isSecure() may not actually be required
            , 'root'    => $_SERVER['SERVER_NAME'] // for live!
            // , 'root'    => "192.168.111.151" // for testing(!!!)
            , 'api'     => '/WebAPI'
            , 'version' => '/v1.5'
        );
        $this->apiBase = implode($this->apiInfo);
    }

    public function canUse() {
        // items to check for:
        // 1. existence of username / password
        // 2. username / password match in the database
        // 3. existence of override, or correct version number
        // 4. server is accessible (check with 400 timeout, without username password?)
        // 5. (more?)

        if (!isset($GLOBALS['apiUsername']) || empty($GLOBALS['apiUsername'])) {
            Log::warn("Attempted to use WebAPI Remoting, but apiUsername was not set in config.php!", Enums::NSP_WEBAPI);
            return false;
        }
        if (!isset($GLOBALS['apiPassword']) || empty($GLOBALS['apiPassword'])) {
            Log::warn("Attempted to use WebAPI Remoting, but apiPassword was not set in config.php!", Enums::NSP_WEBAPI);
            return false;
        }
        if ($this->logic->version->compareToCurrent("15.4") < 0) {
            // current version is less than 15.4
            // check for the override, as we have a shim which works with older versions,
            // but is selectively installed
            if (!isset($GLOBALS['cacheClearOverride']) || !$GLOBALS['cacheClearOverride']) {
                Log::warn("Attempted to use WebAPI Remoting, but the current version is too low and cacheClearOverride is not set! Current version: " . $this->logic->version->current(false), Enums::NSP_WEBAPI); // pass false to keep as string
                return false;
            }
        }
        $users = $this->db->users->match(array(
            "UserName" => $GLOBALS['apiUsername'],
            "Password" => $GLOBALS['apiPassword']
        ));
        if (empty($users)) {
            // invalid credentials!
            Log::warn("Attempted to use WebAPI Remoting, but the provided username/password from config.php was invalid!", Enums::NSP_WEBAPI);
            return false;
        }
        $user = $users[0];
        if ($user->Deleted === true) {
            Log::warn("Attempted to use WebAPI Remoting, but the API User is marked as deleted! Username: " . $GLOBALS['apiUsername']);
            return false;
        }
        if ($user->Enabled === false) {
            Log::warn("Attempted to use WebAPI Remoting, but the API User is marked as not enabled! Username: " . $GLOBALS['apiUsername']);
            return false;
        }
        if ($user->SystemUsers === false) {
            Log::warn("Attempt to use the WebAPI Remoting found that the API User has SystemUsers set to false! Username: " . $GLOBALS['apiUsername']);
            // non blocking -- shouldn't return false, but should be logged so we can fix this as we find them (consider updating in here to set SystemUsers to true? not really ideal)
        }

        // make a call to ClubSpeedCache/clear in order to determine whether or not we have valid credentials
        $callName = '/ClubSpeedCache/clear';
        $apiUrl = $this->getApiUrl($callName);
        try {
            $response = \Httpful\Request::get($apiUrl)
                ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
                ->timeoutIn(10) // timeout after 10 seconds
                ->send();
        }
        catch(\Exception $e) {
            Log::warn("Attempted to use WebAPI Remoting, but the test call threw an exception! Error: " . $e->getMessage());
            return false;
        }
        if($response->code != '200') {
            if ($response->code == '401')
                Log::warn("Attempted to use WebAPI Remoting, but the apiUsername and apiPassword were invalid! Check the API config and Users table for proper credentials! Received status code: " . $response->code, Enums::NSP_WEBAPI);
            else
                Log::warn("Attempted to use WebAPI Remoting, but the server was inaccessible or unusable! Received status code: " . $response->code, Enums::NSP_WEBAPI);
            return false;
        }
        return true;
    }

    /**
     * A helper function (could really be moved to a utility class)
     * to determine whether or not the current connection is being secured.
     *
     * @return boolean True if HTTPS is set (but not to 'off') or SERVER_PORT is 443, false if not.
     */
    protected final function isSecure() { // should really be a utility function
        // check for HTTPS and not set to 'off' (IIS compatability) -- if that fails, assume port of 443 is HTTPS
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    /**
     * A helper function to assist with appending a specific API Call name to the base API URL.
     *
     * @param string $callName The name of the specific WebApi call.
     * @return string The call name appended to the base API URL.
     */
    private function getApiUrl($callName) {
        return $this->apiBase . $callName;
    }

    /**
     * Internal helper function to help determine whether or not
     * the apiUsername and the apiPassword are set in config.php
     *
     * @return void
     * @throws InvalidArgumentException if $GLOBALS['apiUsername'] is either not set or empty.
     * @throws InvalidArgumentException if $GLOBALS['apiPassword'] is either not set or empty.
     */
    private function checkUsernamePasswordAreSet() {
        if (!isset($GLOBALS['apiUsername']) || empty($GLOBALS['apiUsername']))
            throw new \InvalidArgumentException("WebAPI received invalid credentials!");
        if (!isset($GLOBALS['apiPassword']) || empty($GLOBALS['apiPassword']))
            throw new \InvalidArgumentException("WebAPI received invalid credentials!");
    }

    /**
     * Handles a response returned from the httpful library,
     * checking for any codes that are non-200 and throwing an exception.
     *
     * Note that this very well may change in the future, but this is
     * an attempt to get a more useful error message since httpful doesn't seem
     * to throw useful error messages in some situations.
     *
     * @param Httpful\Response $response The response object returned from an httpful call.
     * @return Httpful\Response The unmodified response object, if successful.
     * @throws Exception if the return code of the response was not 200.
     */
    private function handleResponse(&$response) {
        if ($response->code != 200) {
            Log::error("Received status code " . $response->code . " back from WebAPI! Raw body: " . $response->raw_body, Enums::NSP_WEBAPI);
            throw new \Exception($response->raw_body, $response->code); // find a better way to handle this??
        }
        Log::debug("Made a successful call to: " . $response->request->uri, Enums::NSP_WEBAPI);
    }

    public function startRace($heatId) {
        $this->checkUsernamePasswordAreSet();
        $callName = '/Race/Start/' . $heatId;
        $apiUrl = $this->getApiUrl($callName);
        $response = \Httpful\Request::get($apiUrl)
            ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
            ->send();
        $this->handleResponse($response);
    }

    public function stopRace($trackId) {
        $this->checkUsernamePasswordAreSet();
        $callName = '/Race/Stop/' . $trackId;
        $apiUrl = $this->getApiUrl($callName);
        $response = \Httpful\Request::get($apiUrl)
            ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
            ->send();
        $this->handleResponse($response);
    }

    /**
     * A pointer to the remote WebApi call to clear the MainEngine's cache.
     *
     * Note that this function will most likely change in the future 
     * to allow for internal response return handling.
     *
     * @return void
     */
    public function clearCache() {
        // should we also have a version check here before attempting to call?
        $this->checkUsernamePasswordAreSet();
        $callName = '/ClubSpeedCache/clear';
        $apiUrl = $this->getApiUrl($callName);
        $response = \Httpful\Request::get($apiUrl)
            ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
            ->timeoutIn(3) // timeout after 3 seconds
            ->send();
        return $this->handleResponse($response);
    }

    /**
     * A pointer to the remote WebApi call to process a PCCharge payment.
     *
     * Note that this function will most likely change in the future 
     * to allow for internal response return handling and parameters.
     *
     * @return void
     */
    public function processPayment($params = array()) {

        // this is being deprecated for our custom omnipay PCCharge driver

        if (empty($params))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received an empty set of params!");
        if (!isset($params['card']) || empty($params['card']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty credit card object!");
        if (!isset($params['check']) || empty($params['check']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty check!");
        
        $card = $params['card'];
        if (!isset($card['firstName']) || empty($card['firstName']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.firstName!");
        if (!isset($card['lastName']) || empty($card['lastName']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.lastName!");
        if (!isset($card['expiryMonth']) || empty($card['expiryMonth']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.expiryMonth!");
        if (!isset($card['expiryYear']) || empty($card['expiryYear']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.expiryYear!");
        if (!isset($card['postcode']) || empty($card['postcode']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.postcode!");
        if (!isset($card['address1']) || empty($card['address1']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.address1!");
        if (!isset($card['cvv']) || empty($card['cvv']))
            throw new \RequiredArgumentMissingException("WebApi ProcessPayment received a null or empty card.cvv!");
        
        $checkId = \ClubSpeed\Utility\Convert::toNumber(@$params['check']['checkId']);
        if (!isset($checkId) || is_null($checkId) || !is_int($checkId))
            throw new \RequiredArgumentMissingException("Payment processor received an invalid format for checkId! Received: " . @$params['checkId']);
        $checkTotals = $this->logic->checkTotals->get($checkId);
        if (!isset($checkTotals) || is_null($checkTotals) || empty($checkTotals))
            throw new \InvalidArgumentValueException("Payment processor received a checkId which could not be found in the database! Received: " . $checkId);
        $checkTotals = $checkTotals[0];
        if ($checkTotals->CheckStatus != 0)
            throw new \InvalidArgumentValueException("Payment processor received a checkId with a status other than 0 (open)! Found Check.Status: " . $checkTotals->CheckStatus);
        $this->logic->checks->applyCheckTotal($checkTotals->CheckID); // ensure that the checktotal is stored on the checks record, for backwards compatibility
        $check = $this->logic->checks->get($checkTotals->CheckID);
        $check = $check[0];

        $expiry = str_pad($card['expiryMonth'], 2, '0', STR_PAD_LEFT) . substr($card['expiryYear'], -2);
        $data = array(
            "CreditCardNo"      => $card['number']
            , 'AccountName'     => $card['firstName'] . ' ' . $card['lastName'] // concatenate first + last before sending to webapi
            , 'ExpirationDate'  => $expiry // this needs to be in 'MMYY' string format
            , 'Zip'             => $card['postcode']
            , 'Address'         => $card['address1']
            , 'CVV'             => $card['cvv']
            , 'CheckID'         => $checkId
            , 'CardIssuer'      => ''       // unknown
            , 'TaxExempt'       => false    // override to false
            , 'IsCommercial'    => false    // override to false
            , 'TaxAmount'       => $checkTotals->CheckTax // this is not available directly on the check record - grab from the calculated view
            , 'AmountToCharge'  => 0.01 // USE 2.00 FOR PCCHARGE TESTING - should come back declined $check->CheckTotal // use the total (INCLUDING THE TAX, NOT THE SUBTOTAL!!!)
        );

        $callName = '/PCCharge/ProcessPayment';
        $apiUrl = $this->getApiUrl($callName);

        // we will have to handle the response separately, as 200s can technically be failures
        // and is based on the return data

        $response = \Httpful\Request::post($apiUrl)
            ->sendsJson()
            // ->expects('application/json')
            ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
            ->body(json_encode($data))
            ->send();

        if ($response->code != 200) // should we expose the raw_body when not 200? will designate a server failure (most likely 500)
            throw new \CSException($response->raw_body, $response->code);
        $data = (array)$response->body;

        pr($data);
        die();

        // $data = array( // TEST DATA -- REPRESENTS A SUCCESSFUL CREDIT CARD PURCHASE
        //     'ProcessingTime' => 9421.5015
        //     , 'Result' => 'CAPTURED'
        //     , 'AuthorizationCode' => '07763P'
        //     , 'ReferenceNumber' => ''
        //     , 'TroutD' => 2126
        //     , 'TransactionDate' => '2014-10-02T10:41:50.9158084-07:00'
        //     , 'ErrorCode' => 0
        //     , 'ErrorDescription' => ''
        //     , 'AVS' => 'N'
        //     , 'CVV2' => ''
        //     , 'AmountDue' => 0.01
        //     , 'CardIssuer' => 'MC'
        //     , 'XMLResponse' =>
        //     '<XML_REQUEST>
        //             <USER_ID>User1</USER_ID>
        //             <TROUTD>2126</TROUTD>
        //             <RESULT>CAPTURED</RESULT>
        //             <AUTH_CODE>07763P</AUTH_CODE>
        //             <AVS_CODE>N</AVS_CODE>
        //             <TICKET>2365</TICKET>
        //             <INTRN_SEQ_NUM>2126</INTRN_SEQ_NUM>
        //             <IND>NN</IND>
        //             <PROC_RESP_CODE>0</PROC_RESP_CODE>
        //             <CMRCL_TYPE>0</CMRCL_TYPE>
        //             <PURCH_CARD_TYPE>0</PURCH_CARD_TYPE>
        //             <PS2000>MWEKBX8ZC      1002A 01</PS2000>
        //         </XML_REQUEST>'
        //     , 'ResponseText' => ''
        //     , 'ResultCode' => 0
        // );


        // expected response structure
        // {
        //   "ProcessingTime": 0,
        //   "Result": "",
        //   "AuthorizationCode": "",
        //   "ReferenceNumber": "",
        //   "TroutD": "",
        //   "TransactionDate": "",
        //   "ErrorCode": "",
        //   "ErrorDescription": "",
        //   "AVS": "",
        //   "CVV2": "",
        //   "AmountDue": 0,
        //   "CardIssuer": "",
        //   "XMLResponse": "",
        //   "ResponseText": "",
        //   "ResultCode": ""
        // }
        if (strtolower($data['Result']) == 'captured' || strtolower($data['Result']) == 'approved') {

            $transactionDate = \ClubSpeed\Utility\Convert::toDateForServer(new \DateTime($data['TransactionDate']));
            // success!
            // build a payment record, update necessary items
            $payment                  = $this->logic->payment->dummy();
            $payment->AVS             = $data['AVS'];
            $payment->CheckID         = $checkId;
            $payment->ExtCardType     = $data['CardIssuer'];
            $payment->PayAmount       = $check->CheckTotal;
            $payment->PayDate         = $transactionDate;
            $payment->PayStatus       = 1; // PayStatus.PAID from VB
            $payment->PayTax          = $checkTotals->CheckTax;
            $payment->PayTerminal     = 'api';// use this?
            $payment->PayType         = 2; // always credit card when through pccharge?
            $payment->ResponseTime    = (int)$data['ProcessingTime'];
            $payment->TransactionDate = $transactionDate; // doesn't seem to ever be set, but since we have the data anyways..
            $payment->TroutD          = $data['TroutD'];
            $payment->UserID          = 1; // probably should be non-nullable, onlinebooking userId?
            $this->logic->payment->create($payment);

            // Public Enum PayStatus
            // PAID = 1
            //     VOID = 2
            // End Enum

            // Public Enum VoidType ' pccharge
            //     VOIDSale = 3
            //     VOIDRefund = 6
            // End Enum

            // pr("success!");
            // die();
            // return true; // or something
        }
        else {
            // more testing required here
            if (isset($data['ErrorCode']) && !empty($data['ErrorCode'])) {
                throw new \CSException($data['ErrorCode'] . ": " . $data['ErrorDescription']);
            }
            else {
                throw new \CSException($data['Result'] . ": " . $data['AuthorizationCode']);
            }
        }
        // die();
        // return $response;
    }

    /**
     * A pointer to the remote WebApi call to combine customers.
     *
     * Note that this function will most likely change in the future 
     * to allow for internal response return handling and parameters.
     *
     * @return void
     */
    public function combineCustomers($custId1, $custId2) {
        $callName = '/Customer/Combine';
        $apiUrl = $this->getApiUrl($callName);
        $data = array(
            // todo: get expected process payment items -- probably from the argument list
            // todo: determine the structure that Customer/Combine expects
        );
        // $response = Request::post($apiUrl)
        //     ->sendsJson()
        //     ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
        //     ->body(json_encode($data))
        //     ->send();
        // return $response;
    }
}