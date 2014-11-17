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
     * This function performs a GET or POST call via the Club Speed PHP API.
     * It returns null if there is any sort of error, or the results if successful.
     * @param $url The URL to call, with all GET parameters included.
     * @param null $params POST parameters to include, if any.
     * @param string $verb Whether the call is a 'GET' or 'POST'.
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


    //TODO: Documentation
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

    public static function getJSON($resource, $queryParams = array())
    {
        self::initialize();
        $queryParams['key'] = self::$apiKey;        
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
    
    public static function getListOfChannels()
    {
        self::initialize();
        $urlVars = array('key' => self::$apiKey);
        $url = self::$apiURL . '/channel/all.json?' . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    public static function getDetailsOnChannel($channelId)
    {
        self::initialize();
        $urlVars = array('key' => self::$apiKey);
        $url = self::$apiURL . "/channel/$channelId.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    public static function getBookingSettings()
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey, 'group' => 'Booking');
        $url = self::$apiURL . "/settings/get.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    public static function getSettingsFor($terminalName)
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey, 'group' => $terminalName);
        $url = self::$apiURL . "/settings/get.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

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
    
    private static function updateBookingSetting($newSettingName,$newSettingValue)
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey);
        $url = self::$apiURL . "/controlPanel/Booking/$newSettingName?" . http_build_query($urlVars);
        $params = array('value' => $newSettingValue);

        $result = self::call($url,$params,'PUT');

        $response = $result['response'];
        $error = $result['error'];

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

    public static function updateBookingSettings($newSettings)
    {
        self::initialize();
        foreach($newSettings as $newSettingName => $newSettingValue)
        {
            $result = self::updateBookingSetting($newSettingName,$newSettingValue);

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

    public static function updateSettingsFor($terminalName,$newSettings)
    {
        self::initialize();
        foreach($newSettings as $newSettingName => $newSettingValue)
        {
            $result = self::updateSettingFor($terminalName,$newSettingName,$newSettingValue);

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

    private static function updateSettingFor($terminalName,$newSettingName,$newSettingValue)
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey);
        $url = self::$apiURL . "/controlPanel/$terminalName/$newSettingName?" . http_build_query($urlVars);
        $params = array('value' => $newSettingValue);

        $result = self::call($url,$params,'PUT');

        $response = $result['response'];
        $error = $result['error'];

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

    public static function getSupportedPaymentTypes()
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey);
        $url = self::$apiURL . "/processPayment.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    public static function getReport_Payments($start = null, $end = null)
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey);
        if ($start != null) { $urlVars['start'] = $start; }
        if ($end != null) { $urlVars['end'] = $end; }

        $url = self::$apiURL . "/reports/payments.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    public static function getReport_SummaryPayments($start = null, $end = null)
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey);
        if ($start != null) { $urlVars['start'] = $start; }
        if ($end != null) { $urlVars['end'] = $end; }

        $url = self::$apiURL . "/reports/payments_summary.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

    public static function getReport_DetailedSales($start = null, $end = null, $show_by_opened_date = 'false')
    {
        self::initialize();
        $urlVars = array('key' => self::$privateKey);
        if ($start != null) { $urlVars['start'] = $start; }
        if ($end != null) { $urlVars['end'] = $end; }
        $urlVars['show_by_opened_date'] = isset($show_by_opened_date) ? $show_by_opened_date : 'false';

        $url = self::$apiURL . "/reports/sales.json?" . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null && property_exists($response,'body'))
        {
            return $response->body;
        }
        else
        {
            return null;
        }
    }

}