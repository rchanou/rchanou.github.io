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

    /*
       ############
       # SETTINGS #
       ############
    */

    public static function getBookingSettings()
    {
        self::initialize();

        $url = self::$apiURL . '/settings/get.json?group=Booking&key=' . self::$privateKey;

        $result = self::call($url);

        $result = $result['response'];

        if ($result !== null && isset($result->body->settings))
        {
            $settings = array();
            foreach($result->body->settings as $currentSetting)
            {
                $settings[$currentSetting->SettingName] = $currentSetting->SettingValue;
            }

            if (isset($settings['onlineBookingPaymentProcessorSettings']))
            {
                $settings['onlineBookingPaymentProcessorSettings'] = json_decode($settings['onlineBookingPaymentProcessorSettings']);
            }
            return $settings;
        }
        else
        {
            return null;
        }
    }

    /*
       ##########
       # STEP 1 #
       ##########
    */

    public static function getAvailableBookingsForDropdown($startDate = null,$endDate = null)
    {
        self::initialize();

        $url = self::$apiURL . '/bookingavailability/range.json?key=' . self::$privateKey
                . ($startDate === null ? '' : '&start=' . $startDate)
                . ($endDate === null ? '' : '&endDate=' . $endDate);

        $result = self::call($url);

        $result = $result['response'];

        if ($result !== null && isset($result->body->bookings))
        {
            $heatTypes = array();
            $heatTypes[] = array("heatTypeId" => -1,
                           "name" => "All",
                           "heatSpotsAvailableOnline" => "999");
            foreach($result->body->bookings as $currentAvailableBooking)
            {
                $heatTypes[] = array("heatTypeId" => $currentAvailableBooking->heatTypeId,
                    "name" => $currentAvailableBooking->heatDescription,
                    "heatSpotsAvailableOnline" => $currentAvailableBooking->heatSpotsAvailableOnline);
            }
            return $heatTypes;
        }
        else
        {
            return null;
        }
    }

    public static function filterDropdownHeatsByAvailableSpots($heats,$minNumberOfSpots)
    {
        self::initialize();

        $heatsFiltered = array();
        if ($heats != null)
        {
            foreach($heats as $currentHeat)
            {
                if (isset($currentHeat['heatSpotsAvailableOnline']) && $currentHeat['heatSpotsAvailableOnline'] >= $minNumberOfSpots)
                {
                    $heatsFiltered[] = $currentHeat;
                }
            }
        }
        return $heatsFiltered;
    }

    public static function getAvailableBookings($startDate = null,$endDate = null)
    {
        self::initialize();

        $url = self::$apiURL . '/bookingavailability/range.json?key=' . self::$privateKey
            . ($startDate === null ? '' : '&start=' . $startDate)
            . ($endDate === null ? '' : '&endDate=' . $endDate);

        $result = self::call($url);

        $result = $result['response'];
        if ($result !== null && isset($result->body->bookings))
        {
            return $result->body->bookings;
        }
        else
        {
            return null;
        }
    }

    public static function filterHeatsByAvailableSpots($heats,$minNumberOfSpots)
    {
        self::initialize();

        $heatsFiltered = array();
        if ($heats != null)
        {
            foreach($heats as $currentHeat)
            {
                if ($currentHeat->heatSpotsAvailableOnline >= $minNumberOfSpots)
                {
                    $heatsFiltered[] = $currentHeat;
                }
            }
        }
        return $heatsFiltered;
    }

    /*
   ##########
   # STEP 2 #
   ##########
    */

    public static function createClubSpeedAccount($customerData)
    {
        self::initialize();

        $url = self::$apiURL . '/racers/create.json?key=' . self::$privateKey;
        $result = self::call($url,$customerData,'POST');

        $errorMessage = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'body') && property_exists($result->body, 'customerId'))
        {
            return $result->body->customerId;
        }
        else if ($errorMessage !== null)
        {
            return $errorMessage;
        }
        else
        {
            return null;
        }
    }

    public static function loginToClubSpeed($username,$password)
    {
        self::initialize();

        $url = self::$apiURL . '/racers/login.json?key=' . self::$apiKey;

        $customerData = array(
          'username' => $username,
          'password' => $password
        );

        $result = self::call($url,$customerData,'POST');
        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null)
        {
            if (property_exists($result, 'body') && property_exists($result->body, 'customerId'))
            {
                return $result->body;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    public static function loginToClubSpeedViaFacebook($customerData) //Creates account if doesn't exist
    {
        self::initialize();

        $url = self::$apiURL . '/racers/fb_login.json?key=' . self::$privateKey;
        $result = self::call($url,$customerData,'POST');

        $errorMessage = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'body') && property_exists($result->body, 'customerId'))
        {
            return $result->body->customerId;
        }
        else if ($errorMessage !== null)
        {
            return $errorMessage;
        }
        else
        {
            return null;
        }
    }

    public static function isAccountClaimed($email)
    {
        self::initialize();
        $url = self::$apiURL . '/racers/email_is_claimed.json?key=' . self::$privateKey . '&email=' . $email;
        $result = self::call($url);

        $errorMessage = $result['error'];
        $result = $result['response'];

        if ($result !== null && isset($result->body->used))
        {
            return $result->body->used;
        }
        else if ($errorMessage !== null)
        {
            return $errorMessage;
        }
        else
        {
            return null;
        }
    }

    /**
     * Given a short-lived Facebook token, this function exchanges it server-side for a 60-day token.
     * If it cannot do so for any reason, it'll return the short-lived token back to the caller.
     *
     * @param $shortLivedToken
     * @return mixed
     */
    public static function extendFacebookToken($shortLivedToken)
    {
        self::initialize();

        $params = array(
            'grant_type' => 'fb_exchange_token',
            'client_id' => '296582647086963',
            'client_secret' => 'e4edbb2b80ca8784944784643c90cecc',
            'fb_exchange_token' => $shortLivedToken
        );

        $params = http_build_query($params);

        $url = 'https://graph.facebook.com/oauth/access_token?' . $params;
        $result = self::call($url);

        $errorMessage = $result['error'];
        $result = $result['response'];

        if ($result !== null && $result->code == 200 && isset($result->body))
        {
            $parsedString = array();
            parse_str($result->body,$parsedString);

            if (isset($parsedString['access_token']))
            {
                return $parsedString['access_token'];
            }
            else
            {
                return $shortLivedToken;
            }
        }
        else if ($errorMessage !== null)
        {
            return $shortLivedToken;
        }
        else
        {
            return $shortLivedToken;
        }
    }


    /*
    ###################
    # PASSWORD RESETS #
    ###################
    */

    public static function requestPasswordReset($emailAddress)
    {
        self::initialize();

        $emailAddress = array('email' => $emailAddress);

        $url = self::$apiURL . '/passwords/?key=' . self::$privateKey;

        $result = self::call($url,$emailAddress,'POST');

        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return null;
        }
    }

    public static function resetPassword($email,$password,$token)
    {
        self::initialize();

        $passwordResetData = array(
            'email' => $email,
            'password' => $password,
            'token' => $token
        );

        $url = self::$apiURL . '/passwords/?key=' . self::$privateKey;

        $result = self::call($url,$passwordResetData,'PUT');

        $error = $result['error'];
        $result = $result['response'];


        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return false;
        }
        else
        {
            return null;
        }
    }

    /*
    #######################
    # CART - RESERVATIONS #
    #######################
    */

    public static function createOnlineReservation($onlineBookingsId,$quantity,$sessionId,$customersId)
    {
        self::initialize();

        $onlineBookingData = array(
            'onlineBookingsId' => $onlineBookingsId,
            'quantity' => $quantity,
            'sessionId' => $sessionId,
            'customersId' => $customersId
        );

        $url = self::$apiURL . '/reservations/?key=' . self::$privateKey;

        $result = self::call($url,$onlineBookingData,'POST');

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200 && isset($result->body->onlineBookingReservationsId))
            {
                return $result->body->onlineBookingReservationsId;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    public static function deleteOnlineReservation($onlineBookingsReservationId)
    {
        self::initialize();

        $url = self::$apiURL . "/reservations/$onlineBookingsReservationId?key=" . self::$privateKey;

        $result = self::call($url, array(), 'DELETE');

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code')) {
            if ($result->code == 200) {
                return true;
            } else {
                return false;
            }
        } else if ($error !== null) {
            return $error;
        } else {
            return null;
        }
    }

    public static function makeOnlineReservationPermanent($onlineBookingsReservationId)
    {
        self::initialize();

        $url = self::$apiURL . "/reservations/$onlineBookingsReservationId?key=" . self::$privateKey;

        $putData = array(
            "onlineBookingReservationStatusId" => 2
        );

        $result = self::call($url,$putData,'PUT');

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code')) {
            if ($result->code == 200) {
                return true;
            } else {
                return false;
            }
        } else if ($error !== null) {
            return $error;
        } else {
            return null;
        }
    }

    public static function getOnlineReservations()
    {
        self::initialize();

        $url = self::$apiURL . "/reservations/?key=" . self::$privateKey;

        $result = self::call($url);

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code')) {
            if ($result->code == 200 && isset($result->body->reservations)) {
                return $result->body->reservations;
            } else {
                return false;
            }
        } else if ($error !== null) {
            return $error;
        } else {
            return null;
        }
    }

    /*
    ########################
    # CART - VIRTUAL CHECK #
    ########################
    */

    public static function getVirtualCheck($checkDetails)
    {
        self::initialize();

        $url = self::$apiURL . '/checkTotals/virtual?select=checkSubtotal,checkTax,checkTotal,checkDetailSubtotal,checkDetailTax,checkDetailTotal,productId,qty,unitPrice,checkDetailId&key=' . self::$privateKey;

        $result = self::call($url,$checkDetails,'POST');

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200)
            {
                return $result->body;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    /*
    ######################
    # CART - REAL CHECKS #
    ######################
    */

    public static function createCheck($checkDetails)
    {
        self::initialize();

        $url = self::$apiURL . '/checkTotals/?select=checkSubtotal,checkTax,checkTotal,checkDetailSubtotal,checkDetailTax,checkDetailTotal,productId,qty,unitPrice,checkDetailId&key=' . self::$privateKey;

        $result = self::call($url,$checkDetails,'POST');

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200 && isset($result->body->checkId))
            {
                return $result->body->checkId;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    public static function getCheck($checkId)
    {
        self::initialize();


        $url = self::$apiURL . "/checkTotals/$checkId?select=checkSubtotal,checkTax,checkTotal,checkDetailSubtotal,checkDetailTax,checkDetailTotal,productId,qty,unitPrice,checkDetailId&key=" . self::$privateKey;

        $result = self::call($url);

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200)
            {
                return $result->body;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    /*
    ###########
    # PAYMENT #
    ###########
    */

    public static function makePayment($paymentProcessorSettings,$check,$paymentInformation)
    {
        self::initialize();

        $formattedData = $paymentProcessorSettings;
        $formattedData->check = $check;
        $formattedData->card = $paymentInformation;

        $url = self::$apiURL . '/processPayment?&key=' . self::$privateKey;

        $result = self::call($url,$formattedData,'POST');

        $error = $result['error'];
        $result = $result['response'];

        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    /*
    ############
    # SETTINGS #
    ############
    */

    public static function getSettings()
    {
        self::initialize();

        $url = self::$apiURL . "/settings/get.json?group=kiosk&key=" . self::$privateKey;

        $result = self::call($url);

        $error = $result['error'];
        $result = $result['response'];


        if ($result !== null && property_exists($result, 'code'))
        {
            if ($result->code == 200)
            {
                return $result->body;
            }
            else
            {
                return false;
            }
        }
        else if ($error !== null)
        {
            return $error;
        }
        else
        {
            return null;
        }
    }

    /*
    #################
    # CORE API CALL #
    #################
    */

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
        else if ($verb == 'DELETE')
        {
            try {
                $response = \Httpful\Request::delete($url)
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
}