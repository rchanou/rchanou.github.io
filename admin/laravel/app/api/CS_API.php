<?php

/**
 * Class CS_API
 *
 * This static class allows the user to call the Club Speed API via the use of the Httpful library.
 * It also massages and formats the data as appropriate. It is not a literal 1-to-1 mapping to
 * calling the Club Speed API.
 *
 * For any of these calls, a return of NULL is considered an error.
 * Empty arrays/values will be returned in the case of no data.
 */
class CS_API
{
    private static $apiURL;
    private static $apiKey;
    private static $privateKey;

    private function __construct() { }
    private static $initialized = false;

    private static function initialize()
    {
        if (self::$initialized) return;

        self::$apiURL = Config::get('config.apiURL');
        self::$apiKey = Config::get('config.apiKey');
        self::$privateKey = Config::get('config.privateKey');

        self::$initialized = true;
    }

    /**
     * This function performs a GET, POST, or PUT call via the Club Speed PHP API using Httpful.
     * It returns null if there is any sort of error, or the results if successful.
     * @param $url The URL to call, with all GET parameters included.
     * @param null $params POST parameters to include, if any.
     * @param string $verb Whether the call is a 'GET', 'POST', 'PUT', etc.
     * @return [ 'response': data, 'error': data ]
     */
    private static function call($url, $params = null, $verb = 'GET')
    {
        self::initialize();

        $response = null;
        $errorMessage = null;
        if ($verb == 'GET')
        {
            $response = \Httpful\Request::get($url)->send();
        }
        else if ($verb == 'POST')
        {
            try {
                $response = \Httpful\Request::post($url)
                    ->body($params)
                    ->sendsJson()
                    ->send();
            }
            catch (Exception $e)
            {
                //If there was an error, store debugging information in the session
                $errorInfo = array('url' => $url, 'params' => $params, 'verb' => $verb, 'response' => $e->getMessage());
                Session::put('errorInfo', $errorInfo);

                if ($response !== null && property_exists($response, 'body') && property_exists($response->body, 'error'))
                {
                    $errorMessage = $response->body->error;
                }

                $response = null;
                return array('response' => $response,
                    'error' => $errorMessage);
            }
        }
        else if ($verb == 'PUT')
        {
            try {
                $response = \Httpful\Request::put($url)
                    ->body($params)
                    ->sendsJson()
                    ->send();
            }
            catch (Exception $e)
            {
                //If there was an error, store debugging information in the session
                $errorInfo = array('url' => $url, 'params' => $params, 'verb' => $verb, 'response' => $e->getMessage());
                Session::put('errorInfo', $errorInfo);

                if ($response !== null && property_exists($response, 'body') && property_exists($response->body, 'error'))
                {
                    $errorMessage = $response->body->error;
                }

                $response = null;
                return array('response' => $response,
                    'error' => $errorMessage);
            }
        }
        if ($response === null || !property_exists($response, 'code') || $response->code != 200)
        {

            //If there was an error, store debugging information in the session
            $errorInfo = array('url' => $url, 'params' => $params, 'verb' => $verb, 'response' => $response);
            Session::put('errorInfo', $errorInfo);

            if ($response !== null && property_exists($response, 'body') && property_exists($response->body, 'error'))
            {
                $errorMessage = $response->body->error->message;
            }
            $response = null;
        }

        return array('response' => $response,
                     'error' => $errorMessage);
    }

    /**
     * Generic way of doing a simple GET on the Club Speed API. Returns the body of the response if present.
     * Does no other special handling, but is useful for straightforward GET calls.
     *
     * @param $resource The URL (without the '/' prefix or .json suffix) to hit.
     * @param array $queryParams An associative array of URL get parameters that the call expects, if needed.
     * @return mixed Returns the body of the response if present, or null otherwise.
     */
    public static function getJSON($resource, $queryParams = array())
    {
        self::initialize();
        $queryParams['key'] = self::$privateKey;
        $url = self::$apiURL . '/' . $resource . '.json?' . http_build_query($queryParams);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response, 'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    /**
     * Generic way of doing a simple PUT on the Club Speed API. Returns true if successful, false if not, null if the
     * call had a catastrophic failure. Does no other special handling, but is useful for straightforward PUT calls.
     *
     * @param $resource The URL (without the '/' prefix, '/' suffix, or item ID) to hit.
     * @param $id The ID of the item to perform the put to. It gets appended to the resource URL.
     * @param $params An associative array of any necessary URL parameters.
     * @return mixed Returns true if successful, false if there's a failure, and null if there's a catastrophic failure.
     */
    public static function update($resource, $id, $params)
    {
        self::initialize();
        $url = self::$apiURL . "/" . $resource . "/" . $id . "?" . http_build_query(array('key' => self::$privateKey));
        $result = self::call($url, $params, 'PUT');
        $response = $result['response'];

        if (isset($response->code) && $response->code == 200)
        {
            return true;
        }
        else if ($response !== null)
        {
            return false;
        }
        else
        {
            return null;
        }
    }

    public static function login($username,$password)
    {
        self::initialize();

        $urlVars = array('username' => $username,
            'password' => $password,
            'is_admin' => 1,
            'key' => self::$apiKey);

        $url = self::$apiURL . '/users/login.json?' . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body') && property_exists($response->body,'valid'))
        {
            return $response->body->valid;
        }
        else
        {
            return null;
        }
    }

    public static function getListOfChannels()
    {
        self::initialize();
        return self::getJSON('channel/all'); //So the user of CS_API doesn't need to know the URL details, which may change
    }

    public static function getDetailsOnChannel($channelId)
    {
        self::initialize();
        return self::getJSON("channel/$channelId");
    }

    public static function getSettingsFor($terminalName)
    {
        self::initialize();
        $params = array('group' => $terminalName);
        return self::getJSON("settings/get", $params);
    }

    public static function updateSettingsFor($terminalName,$newSettings)
    {
        self::initialize();
        foreach($newSettings as $newSettingName => $newSettingValue)
        {
            $params = array('value' => $newSettingValue);
            $result = self::update("controlPanel/$terminalName",$newSettingName,$params);

            if ($result === false)
            {
                return false;
            }
            if ($result === null)
            {
                return null;
            }
        }
        return true;
    }

    public static function getSupportedPaymentTypes()
    {
        self::initialize();
        return self::getJSON("processPayment");
    }

    public static function getReport_Payments($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }

        return self::getJSON("reports/payments", $params);
    }

    public static function getReport_DetailedSales($start = null, $end = null, $show_by_opened_date = 'false')
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }
        $params['show_by_opened_date'] = isset($show_by_opened_date) ? $show_by_opened_date : 'false';

        return self::getJSON("reports/sales", $params);
    }

    public static function getReport_SummaryPayments($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }

        return self::getJSON("reports/payments_summary", $params);
    }
}