<?php

/**
 * Class CS_API
 *
 * This is a "static" (thanks, PHP!) class allowing access to key Club Speed API functionality.
 *
 */
class CS_API
{

    private static $baseAPIURL;
    private static $apiKey;
    private static $privateKey;

    private function __construct() { }
    private static $initialized = false;

    private static function initialize()
    {
        if (self::$initialized) return;

        self::$initialized = true;
        self::$baseAPIURL = Config::get('config.baseAPIURL');
        self::$apiKey = Config::get('config.apiKey');
        self::$privateKey = Config::get('config.privateKey');
    }

    /**
     * This function executes the given query (as a GET or POST) and returns the result.
     *
     * @param $url The URL of the API to access.
     * @param array $params Parameters, if any, to be POSTed over.
     * @param string $http_verb Whether it's a GET or POST. Defaults to GET.
     * @return mixed The result of the query.
     */
    public static function callApi($url, $params = array(), $http_verb = 'GET')
    {
        /*echo (json_encode($params));
        die();*/

        self::initialize();

        //Set up headers
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_HTTPHEADER => array('Content-type: application/json')
        );

        //If we're sending a POST request, add in additional parameters
        if($http_verb == 'POST')
        {
            $data = json_encode($params);
            $options[CURLOPT_POST] = strlen($data);
            $options[CURLOPT_POSTFIELDS] = $data;
            $options[CURLOPT_TIMEOUT] = 20; //To allow more time for image uploads
            $options[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',
                'Content-Length: ' . strlen($data));
        }

        //Execute the query
        $ch = curl_init($url);

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        //Debugging output:
        /*echo '<div style="color: white; background-color: black; border: 2px solid white; padding: 20px; margin: 20px; word-break: break-word;"><p/><strong>Result of API call to:</strong> ' . $url . '<br/>';
        echo '<strong>cURL Info:</strong> ' . ((json_encode(curl_getinfo($ch))));
        echo "<br/><strong>Data sent:</strong> ";
        echo (json_encode($params));
        echo "<br/><strong>Data received:</strong> ";
        echo (($result));
        echo "<br/><strong>Errors from cURL:</strong> ";
        echo (json_encode(curl_error($ch)));
        echo '</div>';*/
        //die();

        $errorInfo = array('url' => $url, 'params' => $params, 'response' => $result);
        Session::put('errorInfo', $errorInfo);
        Session::put('errorInfoURL',$url);
        Session::put('errorParams',$params);
        Session::put('errorResponse',$result);

        //Return the result to the caller as an associative array
        return json_decode($result,true);
    }

    public static function updateFacebookToken($email,$facebookId,$facebookToken)
    {
        self::initialize();

        $url = self::$baseAPIURL . '/racers/fb_login?key=' . self::$privateKey;

        $params = array (
                            'email' => $email,
                            'facebookId' => $facebookId,
                            'facebookToken' => $facebookToken,
                            'facebookAllowEmail' => 1,
                            'facebookAllowPost' => 1,
                            'facebookEnabled' => 1
                        );

        //Set up headers
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
        );

        $data = json_encode($params);
        $options[CURLOPT_POST] = strlen($data);
        $options[CURLOPT_POSTFIELDS] = $data;
        $options[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',
            'Content-Length: ' . strlen($data));

        //Execute the query
        $ch = curl_init($url);

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        return true;
    }

    public static function addUserToQueue($customerId, $eventId = -1)
    {
        self::initialize();
        $url = self::$baseAPIURL . '/queues?key=' . self::$privateKey;

        $params = array('customerId' => $customerId);
        if ($eventId != -1) //If the customer isn't a walk-in
        {
            $params['eventId'] = $eventId; //Include the eventId
        }

        //Set up headers
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
        );

        $data = json_encode($params);
        $options[CURLOPT_POST] = strlen($data);
        $options[CURLOPT_POSTFIELDS] = $data;
        $options[CURLOPT_HTTPHEADER] = array('Content-Type: application/json',
            'Content-Length: ' . strlen($data));

        //Execute the query
        $ch = curl_init($url);

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        return true;
    }

    public static function getExistingCustomerMatching($firstname,$lastname,$birthdate)
    {
        self::initialize();

        $urlParams = array(
            'where' => array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'birthdate' => $birthdate
            ),
            'key' => self::$privateKey
        );

        $url = self::$baseAPIURL. '/customers/primary.json?' . http_build_query($urlParams);

        //Set up headers
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_SSL_VERIFYPEER => false
        );

        //Execute the query
        $ch = curl_init($url);

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status !== 200)
            return false;

        $result = json_decode($result,true);

        $errorInfo = array('url' => $url, 'params' => $urlParams, 'response' => $result);
        Session::put('errorInfo', $errorInfo);
        Session::put('errorInfoURL',$url);
        Session::put('errorParams',$urlParams);
        Session::put('errorResponse',$result);

        return $result;
    }

    public static function extendFacebookToken($shortLivedToken)
    {
        self::initialize();

        $urlParams = array(
            'grant_type' => 'fb_exchange_token',
            'client_id' => '296582647086963',
            'client_secret' => 'e4edbb2b80ca8784944784643c90cecc',
            'fb_exchange_token' => $shortLivedToken
        );

        $url = 'https://graph.facebook.com/oauth/access_token?' . http_build_query($urlParams);

        //Set up headers
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_SSL_VERIFYPEER => false
        );

        //Execute the query
        $ch = curl_init($url);

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        if ($result !== null)
        {
            $parsedString = array();
            parse_str($result,$parsedString);

            if (isset($parsedString['access_token']))
            {
                return $parsedString['access_token'];
            }
            else
            {
                return $shortLivedToken;
            }
        }
        else
        {
            return $shortLivedToken;
        }
    }

    /**
     * This function is called by anyone wishing to make an API Call to Club Speed. It abstracts out the specific details
     * as to how the calls are made.
     * @param $command The API call to issue. Supported commands: searchByEmail, registerCustomer, getTranslations, getCurrentCulture, getImages, getSettings, checkAPI
     * @param array $params Parameters to be used, if any.
     * @return array|bool|mixed|null False is an error occurred, data otherwise.
     */
    public static function call($command, $params = array())
    {
        self::initialize();

        $url = self::$baseAPIURL;
        $result = false;
        switch ($command)
        {
            case 'searchByEmail':
                $url = $url . '/racers/search.json?field=email&query=' . $params["email"] . '&key=' . self::$apiKey;
                $result = self::callApi($url);
                break;

            case 'registerCustomer':
                //$url = $url . '/racers/create.json' . '?key=' . self::$privateKey;
                $url = $url . '/racers/register.json' . '?key=' . self::$privateKey;
                $result = self::callApi($url,$params,'POST');
                break;

            case 'getTranslations':
                $url = $url . '/translations?namespace=Registration&key=' . self::$apiKey;
                $result = self::callApi($url);
                $resultFormatted = array();
                if ($result == null || array_key_exists("error",$result) || !is_array($result) || !isset($result["translations"]))
                {
                    $result = null;
                }
                else
                {
                    $translations = $result["translations"];
                    //echo json_encode($result["translations"]); die();
                    foreach ($translations as $translation) {
                        if ($translation["value"] != "")
                        {
                            $language = (isset($translation["culture"]) ? $translation["culture"] : 'en-US');
                            $resultFormatted[$language][$translation["name"]] = $translation["value"];
                        }
                    }
                    $result = $resultFormatted;
                }
                break;
            case 'getCurrentCulture':
                $url = $url . '/settings.json' . '?namespace=Registration&name=currentCulture&key=' .self::$privateKey;
                $result = self::callApi($url);
                $result = isset($result['settings'][0]['value']) ? $result['settings'][0]['value'] : 'en-US';
                break;
            case 'getEnabledCultures':
                $url = $url . '/settings.json' . '?namespace=Registration&name=enabledCultures&key=' .self::$privateKey;
                $result = self::callApi($url);
                $result = isset($result['settings'][0]['value']) ? $result['settings'][0]['value'] : '[]';
                $result = json_decode($result);
                break;
            case 'sendMissingStrings':
                $url = $url . '/translations/batch?key=' . self::$privateKey;
                $result = self::callApi($url,$params,'POST');
                break;
            case 'getImages':
                $url = $url . '/settings/getImages.json?app=kiosk' . '&key=' .self::$privateKey;
                $result = self::callApi($url);
                break;
            case 'getSettings':
                $url = $url . '/settings/get.json' . '?group=kiosk&key=' .self::$privateKey;
                $resultBeforeProcessing = self::callApi($url);
                if ($resultBeforeProcessing === null || (is_array($resultBeforeProcessing) && array_key_exists("error",$resultBeforeProcessing)))
                {
                    $result = null;
                }
                else
                {
                    $url = self::$baseAPIURL . '/settings/get.json' . '?group=Registration&key=' .self::$privateKey;
                    $resultBeforeProcessingRegistration = self::callApi($url);

                    if (!($resultBeforeProcessingRegistration === null || (is_array($resultBeforeProcessingRegistration) && array_key_exists("error",$resultBeforeProcessingRegistration))))
                    {
                        $resultBeforeProcessing['settings'] = array_merge($resultBeforeProcessing['settings'],$resultBeforeProcessingRegistration['settings']);
                    }

                    $result = array();

                    foreach($resultBeforeProcessing["settings"] as $currentSettingKey => $currentSettingValue)
                    {
                        $result[$currentSettingKey] = $resultBeforeProcessing["settings"][$currentSettingKey]["SettingValue"];
                    }
                }
                break;
            case 'checkAPI':
                $url = $url . '/version/api.json?key=' . self::$apiKey;
                $result = self::callApi($url);

                break;
            case 'getCameraIP':
                $terminalName = $params["terminalName"]; //The terminal to pull the camera's IP from

                $urlParameterLabel = "CamIP"; //The label for the IP address of the camera in the URL - TODO: This may need to be a config setting

                $url = $url . '/settings/get.json' . '?group=' . $terminalName . '&key=' .self::$privateKey;

                $resultBeforeProcessing = self::callApi($url);
                if ($resultBeforeProcessing === null || (is_array($resultBeforeProcessing) && array_key_exists("error",$resultBeforeProcessing)))
                {
                    $result = null;
                }
                else
                {
                    $result = array();

                    //return "192.168.111.133/IMAGE.JPG"; //For testing purposes, hard-coding to local IP Camera

                    foreach($resultBeforeProcessing["settings"] as $currentSettingKey => $currentSettingValue)
                    {
                        $result[$currentSettingKey] = $resultBeforeProcessing["settings"][$currentSettingKey]["SettingValue"];
                    }

                    if (array_key_exists("url",$result)) //If we pulled the setting successfully
                    {
                        $parsedParameters = array();
                        $urlParts = parse_url($result["url"]); //Break the URL into its components
                        if (!isset($urlParts['query']))
                        {
                            return null;
                        }
                        parse_str($urlParts['query'],$parsedParameters); //Parse the 'query' component and put the results in $parsedParameters
												
                        // Make a lowercased copy of the keys. This resolves inconsistent casing: CamIP, CamIp, CaMiP, et al
                        foreach($parsedParameters as $key => $value) {
                          $parsedParameters[strtolower($key)] = $value;
                        }

                        return !array_key_exists(strtolower($urlParameterLabel), $parsedParameters) ? null : $parsedParameters[strtolower($urlParameterLabel)]; //Return the URL parameter we're looking for (the URL of the IP Camera) or null if not found
                    }
                    else
                    {
                        return null;
                    }
                }
                break;
            default:
                return false;
                break;
        }

         //Useful debugging info
        /*echo "Command: ";
        var_dump($command);
        echo "<br/>Params: ";
        var_dump($params);
        echo "<br/>Result: ";
        var_dump($result);
        echo "<br/>";*/
        //die();

        if ($result === null || (is_array($result) && array_key_exists("error",$result)) ) //If an error occurred
        {
            $result = false;
        }

        return $result;
    }

    /**
     * This function pings the Club Speed API by trying to get the API version.
     * If any connectivity issue occurs, this returns true.
     * Otherwise, it returns false.
     * @return bool True if there's a connectivity problem, or false otherwise.
     */
    public static function cannotConnectToClubSpeedAPI()
    {
        return !self::call("checkAPI");
    }

    /**
     * This function is called to check for a potential language changed.
     * This is determined by checking for the existence of "currentCultureChanged" in the session.
     * If a change was requested, current strings and culture are switched if the desired new language exists in memory.
     */
    public static function checkForLanguageChange()
    {
        if (Session::has("currentCultureChanged"))
        {
            $newCulture = Session::get("currentCultureChanged");
            $translations = Session::get("translations");
            if (array_key_exists($newCulture, $translations))
            {
                $strings = $translations[$newCulture];
                $strings["cultureNames"] = Strings::getCultureNames();
                $settings = Session::get('settings');

                //If the new-style waiver texts have been defined for the new language, use those instead of the old Waiver1 and Waiver2 settings
                if (isset($settings['useNewWaivers']) && $settings['useNewWaivers'] === true)
                {
                    if (isset($strings['str_WaiverAdult']))
                    {
                        $settings['Waiver1'] = $strings['str_WaiverAdult'];
                    }
                    if (isset($strings['str_WaiverChild']))
                    {
                        $settings['Waiver2'] = $strings['str_WaiverChild'];
                    }
                }

                Session::put('strings',$strings);
                Session::put('settings',$settings);
                Session::put("currentCulture",$newCulture);
                Session::put("currentCultureFB", self::convertCultureToFacebook($newCulture));
            }
        }
    }

    /**
     * This function takes the standard localization format, "en-US", and converts it to Facebook's format, "en_US".
     * It also recognizes formats that Facebook does not directly support, and converts to the nearest one instead.
     * @param string $currentCulture The current culture in a standard en-US format.
     * @return string Facebook's format of that culture: en_US.
     */
    public static function convertCultureToFacebook($currentCulture)
    {
        $currentCulture = strtolower(substr($currentCulture,0,2)) . '_' . strtoupper(substr($currentCulture,3,2));
        switch ($currentCulture)
        {
            case "es_MX":
            case "es_PR":
                return "es_LA";
                break;
            case "ar_AE":
                return "ar_AR";
                break;
        }
        return $currentCulture;
    }

    /**
     * This function is used to check if we need to redirect to the home page for any reason.
     * This would happen if future steps were navigated to before prior steps were completed, or if past steps
     * were to be visited after registration had completed.
     * @return bool True if a session was not initialized and we are at any step other than step1, or if a session is
     * complete and we are at any step other than the last. False otherwise.
     */
    public static function sessionIsInvalid()
    {
        if (!Session::has('initialized') || Session::has('sessionComplete'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function log($message, $terminal = 'Club Speed Registration', $username = '')
    {
        self::initialize();

        $url = self::$baseAPIURL . '/logs?key=' . self::$privateKey;
        $params = array(
            'message'  => $message,
            'terminal' => $terminal,
            'username' => $username
        );

        return self::callApi($url, $params, 'POST');
    }

    //Used to reconstruct custom URL parameters upon returning to main screen - (localcam or terminal)
    public static function getStep1URL()
    {
        self::initialize();

        $step1URL = 'step1';
        if (Session::has('ipcam'))
        {
            $step1URL = $step1URL . '?&terminal=' . Session::get('ipcam');
        }
        else if (Session::has('localcam'))
        {
            $step1URL = $step1URL . '?&localcam=1';
        }
        return $step1URL;
    }
}