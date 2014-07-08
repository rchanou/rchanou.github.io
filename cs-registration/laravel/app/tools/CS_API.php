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
            CURLOPT_HTTPHEADER => array('Content-type: application/json')//,
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
/*        echo '<div style="color: white; background-color: black; border: 2px solid white; padding: 20px; margin: 20px; word-break: break-word;"><p/><strong>Result of API call to:</strong> ' . $url . '<br/>';
        echo '<strong>cURL Info:</strong> ' . ((json_encode(curl_getinfo($ch))));
        echo "<br/><strong>Data sent:</strong> ";
        echo (json_encode($params));
        echo "<br/><strong>Data received:</strong> ";
        echo (($result));
        echo "<br/><strong>Errors from cURL:</strong> ";
        echo (json_encode(curl_error($ch)));
        echo '</div>';
        die();*/

        //Return the result to the caller as an associative array
        return json_decode($result,true);
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
                $url = $url . '/racers/search.json?filter=email&query=' . $params["email"] . '&key=' . self::$apiKey;
                $result = self::callApi($url);
                break;

            case 'registerCustomer':
                $url = $url . '/racers/create.json' . '?key=' . self::$privateKey;
                $result = self::callApi($url,$params,'POST');
                break;

            case 'getTranslations':
                $url = $url . '/translations/getNamespace.json' . '?namespace=Interfaces.Common&key=' .self::$apiKey;
                $result = self::callApi($url);
                $resultFormatted = array();
                if ($result == null || array_key_exists("error",$result) || !is_array($result))
                {
                    $result = null;
                }
                else
                {
                    foreach($result["translation"] as $language => $translations)
                    {
                        foreach($translations as $currentStringLabel => $currentStringValue)
                        {
                            $resultFormatted[$language][$currentStringLabel] = $currentStringValue["value"];
                        }
                    }
                    $result = $resultFormatted;
                }
                break;
            case 'getCurrentCulture':
                $url = $url . '/settings/get.json' . '?group=MainEngine&setting=currentCulture&key=' .self::$privateKey;

                $result = self::callApi($url);
                if ($result == null || array_key_exists("error",$result) || !is_array($result) || !array_key_exists("CurrentCulture",$result["settings"]))
                {
                    $result = "en-US";
                }
                else
                {
                    $result = $result["settings"]["CurrentCulture"]["SettingValue"];
                }
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
            default:
                return false;
                break;
        }

         //Useful debugging info
/*        echo "Command: ";
        var_dump($command);
        echo "<br/>Params: ";
        var_dump($params);
        echo "<br/>Result: ";
        var_dump($result);
        echo "<br/>";
        die();*/

        if ($result === null || (is_array($result) && array_key_exists("error",$result)) ) //If an error occurred
        {
            $result = false;
        }

        return $result;
    }
}