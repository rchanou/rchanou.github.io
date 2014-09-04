<?php

namespace ClubSpeed\Remoting;

// Grab httpful
require_once(__DIR__.'../../../httpful/httpful.phar');

/**
 * The CSWebApi class which contains references to remote methods
 * on the .NET WebApi which need to be executed by the PHP api
 * for interfacing with the MainEngine.
 */
class CSWebApi {

    /**
     * The remote api info broken into an associative array.
     */
    private $apiInfo;

    /**
     * The remote api info imploded into a single base url.
     */
    private $apiBase;

    /**
     * Creates a new instance of the CSWebApi class.
     */
    public function __construct() {
        $this->apiInfo = array(
            // 'protocol'  => ($this->isSecure() ? 'https' : 'http').'://'
            'protocol'  => 'https://' // https is required for WebAPI -- isSecure() may not actually be required
            , 'root'    => $_SERVER['SERVER_NAME']
            // , 'root'    => "ekwigan.clubspeedtiming.com" // for testing(!!!)
            , 'api'     => '/WebAPI'
            , 'version' => '/v1.5'
        );
        $this->apiBase = implode($this->apiInfo);
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
            //throw new \Exception($response->raw_body, $response->code); // find a better way to handle this??
        }
        return $response;
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
        $this->checkUsernamePasswordAreSet();
        $callName = '/ClubSpeedCache/clear';
        $apiUrl = $this->getApiUrl($callName);
        $response = \Httpful\Request::get($apiUrl)
            ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
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
    public function processPayment() {
        $callName = '/PCCharge/ProcessPayment';
        $apiUrl = $this->getApiUrl($callName);
        $data = array(
            // todo: get expected process payment items -- probably from the argument list
            // todo: determine the structure that PCCharge/ProcessPayment expects
        );
        // $response = Request::post($apiUrl)
        //     ->sendsJson()
        //     ->authenticateWith($GLOBALS['apiUsername'], $GLOBALS['apiPassword'])
        //     ->body(json_encode($data))
        //     ->send();
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