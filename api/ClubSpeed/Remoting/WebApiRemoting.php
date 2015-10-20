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
            // , 'root'    => "192.168.111.119" // for testing(!!!)
            , 'api'     => '/WebAPI'
            , 'version' => '/v1.5'
        );
        $this->apiBase = implode($this->apiInfo);
    }

    public function canUse($wait = 30) {
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
        try {
            $response = $this->clearCache();
            if ($response['code'] == 200)
                return true;
            if ($response['code'] == 401)
                Log::warn("Attempted to use WebAPI Remoting, but the apiUsername and apiPassword were invalid! Check the API config and Users table for proper credentials! Info: " . $response['info'], Enums::NSP_WEBAPI);
            else
                Log::warn("Attempted to use WebAPI Remoting, but the server was inaccessible or unusable! Info: " . $response['info'], Enums::NSP_WEBAPI);
            return false;
        }
        catch(\Exception $e) {
            Log::warn("Attempted to use WebAPI Remoting, but the test call threw an exception! Error: " . $e->getMessage());
            return false;
        }
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
    public function clearCache($wait = 60) {
        // should we also have a version check here before attempting to call?
        $this->checkUsernamePasswordAreSet();
        $callName = '/ClubSpeedCache/clear';
        $apiUrl = $this->getApiUrl($callName);

        $ch = curl_init(); // make curl handle
        curl_setopt_array($ch, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $wait,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_USERPWD => $GLOBALS['apiUsername'] . ":" . $GLOBALS['apiPassword'],
            CURLOPT_SSL_VERIFYPEER => false, // bypass the need for a ca cert verification. OPENS US UP TO MITM ATTACKS (technically). this is what httpful was doing anyways.
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
            )
        ));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        $code = $info['http_code'];
        curl_close($ch);
        if ($err) {
            Log::error("Received status code " . $code . " back from WebAPI! Response: " . $response . ", Err: " . $err . ", Info: " . print_r($info, true), Enums::NSP_WEBAPI);
            throw new \CSException($response, $code); // find a better way to handle this??
        }
        else {
            Log::info("WebAPI successful cache clear: " . $response . ", " . print_r($info, true), Enums::NSP_WEBAPI);
        }
        return array(
            'message' => $response,
            'info' => $info,
            'code' => $code
        );
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
