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
        else if ($verb == 'DELETE')
        {
            try {
                $response = \Httpful\Request::delete($url)
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

    public static function create($resource, $object)
    {
        $result =  self::call(
          self::$apiURL . "/$resource?key=" . self::$privateKey,
          $object,
          'POST'
        );
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

    public static function delete($resource, $id)
    {
        self::initialize();
        $url = self::$apiURL . "/" . $resource . "/" . $id . "?" . http_build_query(array('key' => self::$privateKey));
        $result = self::call($url, null, 'DELETE');
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

				$ipAddress = $_SERVER['REMOTE_ADDR'];
				if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
						$ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
				}

				// Check for maximum number of failed logins
				$maxFailedLoginAttempts = 5;
				$maxLoginAttemptTimeoutInMinutes = 5;
				$numFailedLoginAttempts = 0;

				$params = array(
						'limit' => $maxFailedLoginAttempts,
						'where' => array(
							'message' => array('$like' => "ERROR :: Unsuccessful login for \"{$username}\"%"),
							'date'    => array('$gte'  => date('Y-m-d\TH:i:s\Z', time() - ($maxLoginAttemptTimeoutInMinutes * 60))),
							'terminalName' => array('$eq' => 'Club Speed Admin Panel')
							)
						);
				$failedAttemptLogs = self::getLogs($params);

				$numFailedLoginAttempts = count($failedAttemptLogs);
				$attemptsRemaining = $maxFailedLoginAttempts - $numFailedLoginAttempts;

				if($attemptsRemaining <= 0)
				{
						self::log("ERROR :: Unsuccessful login for \"{$username}\" from {$ipAddress}, maximum failed attempts of {$maxFailedLoginAttempts} reached. Account locked out for {$maxLoginAttemptTimeoutInMinutes} minutes.", 'Club Speed Admin Panel');
						return array('result' => false, 'message' => "Account locked out. Maximum attempts reached for \"{$username}\", please try agan in {$maxLoginAttemptTimeoutInMinutes} minutes."); // Not totally accurate, a rolling timeout
				}

        // Attempt login
				$urlVars = array('username' => $username,
            'password' => $password,
            'key'      => self::$apiKey);

        $url = self::$apiURL . '/users/login.json?' . http_build_query($urlVars);

        $result = self::call($url);
        $response = $result['response'];
        $error = $result['error'];

				if(!empty($response) && property_exists($response->body, 'userId'))
				{
						self::log("INFO :: Successful login for \"{$username}\" from {$ipAddress}", 'Club Speed Admin Panel');
						return array('result' => true, 'message' => "Successful login for \"{$username}\"", 'user' => $response->body);
				} elseif(isset($error)) {
						self::log("ERROR :: Unsuccessful login for \"{$username}\" from {$ipAddress}. Attempts remaining: {$attemptsRemaining}. API error given: {$error}", 'Club Speed Admin Panel');
						$attemptsMessage = ($attemptsRemaining === 1) ? "You have {$attemptsRemaining} attempt remaining." : "You have {$attemptsRemaining} attempts remaining.";
						return array('result' => false, 'message' => "Incorrect username or password for \"{$username}\". {$attemptsMessage}");
				} else {
						return array('result' => false, 'message' => 'There was a problem contacting the Club Speed Server. Please try again later or contact support@clubspeed.com if the problem persists.');
				}
    }

    public static function getSpeedScreenChannels(){
      self::initialize();
      return self::getJSON('speedscreenchannels');
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

    public static function createChannel($newChannelNumber)
    {
      self::initialize();
      return self::call(
        self::$apiURL . '/speedscreenchannels?key=' . self::$privateKey,
        array(
          'channelNumber' => $newChannelNumber,
          'channelData' => '{"name":"","options":{},"timelines":{"regular":{"slides":[]},"races":{"slides":[]}},"hash":"4df99bad554397afa30d99d8a8d4a24765d3371c"}',
          'key' => self::$privateKey
        ),
        'POST'
      );
      //return self::call(self::$apiURL . '/screenTemplate?key=' . self::$privateKey, array('screenTemplateName' => '(untitled)', 'key' => self::$privateKey), 'POST');
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

    //This fetches settings from the Settings table, instead of the ControlPanel table
    public static function getSettingsFromNewTableFor($namespace, $name = null)
    {
        self::initialize();
        $params = array('namespace' => $namespace);
        if ($name != null)
        {
            $params['name'] = $name;
        }
        return self::getJSON("settings", $params);
    }

    //This updates settings in the new Settings table, instead of the ControlPanel table
    public static function updateSettingsInNewTableFor($namespace,$newSettings,$newSettingsIds)
    {
        self::initialize();
        foreach($newSettings as $newSettingName => $newSettingValue)
        {
            $params = array('value' => $newSettingValue,
                           'namespace' => $namespace,
                            'name' => $newSettingName);
            $result = self::update('settings',$newSettingsIds[$newSettingName],$params);

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
        return self::getJSON("omnipay");
    }

    public static function getReport_Social()
    {
        self::initialize();

        return self::getJSON("reports/social");
    }
		
		public static function getReport_Payments($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }

        return self::getJSON("reports/payments", $params);
    }

    public static function getReport_BrokerCodes($start = null, $end = null, $show_by_opened_date = 'false')
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }
        $params['show_by_opened_date'] = isset($show_by_opened_date) ? $show_by_opened_date : 'false';

        return self::getJSON("reports/brokers_summary", $params);
    }
		
		public static function getReport_Accounting($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end   != null) { $params['end']   = $end; }

        return self::getJSON("reports/accounting", $params);
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
		
		public static function getReport_EventRepSales($start = null, $end = null, $show_by_opened_date = 'false')
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }
        $params['show_by_opened_date'] = isset($show_by_opened_date) ? $show_by_opened_date : 'false';

        return self::getJSON("reports/event_rep_sales", $params);
    }
		
		public static function getReport_MarketingSourcePerformance($start = null, $end = null, $show_by_opened_date = 'false')
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }
        $params['show_by_opened_date'] = isset($show_by_opened_date) ? $show_by_opened_date : 'false';

        return self::getJSON("reports/marketing_source_performance", $params);
    }

    public static function getReport_SummaryPayments($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }

        return self::getJSON("reports/payments_summary", $params);
    }
		
		public static function getReport_SalesByPOSAndClass($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end   != null) { $params['end']   = $end;   }

        return self::getJSON("reports/sales_by_pos_and_class", $params);
    }

    public static function getReport_EurekasPayments($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }

        $results = self::getJSON("reports/payments/eurekas.json", $params);
        $columnsToFilter = array('Customer ID','Customer Last Name','Customer First Name');
        if ($results !== null && count($results) > 0)
        {
            foreach($results as &$currentRow)
            {
                foreach($currentRow as $currentColumnLabel => &$currentColumnValue)
                {
                    if (in_array($currentColumnLabel,$columnsToFilter))
                    {
                        unset($currentRow->$currentColumnLabel);
                    }
                }
            }
        }
        return $results;
    }

    public static function getReport_EurekasDetailedSales($start = null, $end = null, $show_by_opened_date = 'false')
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }
        $params['show_by_opened_date'] = isset($show_by_opened_date) ? $show_by_opened_date : 'false';

        $results = self::getJSON("reports/sales/eurekas.json", $params);
        $columnsToFilter = array('Customer ID','Customer Last Name','Customer First Name','Product Class Export');
        if ($results !== null && count($results) > 0)
        {
            foreach($results as &$currentRow)
            {
                foreach($currentRow as $currentColumnLabel => &$currentColumnValue)
                {
                    if (in_array($currentColumnLabel,$columnsToFilter))
                    {
                        unset($currentRow->$currentColumnLabel);
                    }
                }
            }
        }
        return $results;
    }

    public static function getReport_EurekasSummaryPayments($start = null, $end = null)
    {
        self::initialize();
        $params = array();
        if ($start != null) { $params['start'] = $start; }
        if ($end != null) { $params['end'] = $end; }

        return self::getJSON("reports/payments_summary/eurekas.json", $params);
    }

    public static function doesServerSupportCacheClearing()
    {
        self::initialize();
        $result = self::getJSON("shim");
        $result = isset($result) ? true : false;
        return $result;
    }

    public static function getListOfTracks()
    {
        self::initialize();
        $result = self::getJSON('tracks/index'); //'/tracks/index.json?&key=

        if (isset($result->tracks))
        {
            $formattedTracks = array();
            foreach($result->tracks as $currentTrack)
            {
                $formattedTracks[$currentTrack->id] = $currentTrack->name;
            }
            return $formattedTracks;
        }
        else
        {
            return null;
        }
    }

    public static function getTranslations($namespace)
    {
        self::initialize();
        $translationsUnformatted =  self::getJSON('translations', array('namespace' => $namespace));
        if (isset($translationsUnformatted->translations))
        {
            $translationsByCulture = array();
            foreach($translationsUnformatted->translations as $currentTranslation)
            {
                $translationsByCulture[$currentTranslation->culture][$currentTranslation->name] = array(
                    'value' => $currentTranslation->value,
                    'id' => $currentTranslation->translationsId);
            }
            return $translationsByCulture;
        }
        else
        {
            return null;
        }

    }

    public static function updateTranslation($newTranslation)
    {
        self::initialize();

        $result = self::update('translations',$newTranslation['translationsId'],$newTranslation['value']);
        return $result;
    }


    public static function updateTranslations($newTranslations)
    {
        self::initialize();

        $allCallsSuccessful = true;
        foreach($newTranslations as $newTranslation)
        {
            $result = self::updateTranslation($newTranslation);
            $callFailed = ($result != true);
            if ($callFailed)
            {
                $allCallsSuccessful = false;
            }
        }
        return $allCallsSuccessful;

    }

    public static function updateTranslationsBatch($newTranslations)
    {
        self::initialize();

        $url = self::$apiURL . '/translations/batch?key=' . self::$privateKey;

        $result = self::call($url,$newTranslations,'PUT');
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null)
        {
            if ((isset($response->code) && $response->code == 200) && !isset($response->body->translations->error))
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

		public static function log($message, $terminal = 'Club Speed PHP API', $username = '')
		{
				self::initialize();

        $url = self::$apiURL . '/logs?key=' . self::$privateKey;
				$params = array(
						'message'  => $message,
						'terminal' => $terminal,
						'username' => $username
						);

        return self::call($url, $params, 'POST');
		}

		public static function getUserTasks($params)
		{
				self::initialize();

				$urlParams = array(
					'limit'  => isset($params['limit'])  ? $params['limit']  : null,
					'page'   => isset($params['page'])   ? $params['page']   : null,
					'offset' => isset($params['offset']) ? $params['offset'] : null,
					'where'  => isset($params['where'])  ? json_encode($params['where']) : null,
					'order'  => isset($params['order'])  ? $params['order']  : null,
					'select' => isset($params['select']) ? json_encode($params['select']) : null, // Could be array or CSV
					'key'    => self::$privateKey
					);

				// Remove empty items
				foreach($urlParams as $key => $value) {
				    if(empty($value)) {
				        unset($urlParams[$key]);
				    }
				}

        $url = self::$apiURL . "/userTasks.json?" . http_build_query($urlParams);

        $result = self::call($url);

				return isset($result['error']) ? $result : $result['response']->body;
		}

		public static function getLogs($params)
		{
				/*
				4. Find Logs
				Verb: GET
				Url: https://vm122.clubspeedtiming.com/api/index.php/logs?key=cs-dev&where={"terminal": "ClubSpeed PHP API", "message": {"$like": "ERROR%"}}&limit=3&offset=10&order=logsId DESC
				Response Body:
				[
					{
						"logsId": 15166,
						"message": "ERROR :: Check #3667: CheckDetail #9107: Unable to clear WebAPI cache! Received code: 404",
						"date": "2015-02-06T13:15:39",
						"terminal": "ClubSpeed PHP API",
						"username": null
					},
					{
						"logsId": 15148,
						"message": "ERROR :: Check #3666: CheckDetail #9106: Unable to clear WebAPI cache! Received code: 404",
						"date": "2015-02-06T13:15:34",
						"terminal": "ClubSpeed PHP API",
						"username": null
					}
				]
				Notes:
				Biggest difference here is with the Url parameters.
				1. limit -- optional, defaults to 100.
				2. page -- optional, extension to set offset based on limit. use of offset will override page.
				3. offset -- optional, defaults to 0.
				4. where -- optional, must be parsable JSON, set of available options listed below, example above will provide records where terminal equals "ClubSpeed PHP API" and message is like "ERROR%".
				5. order -- optional, typically defaults to :table_id ASC. In the case of logs, it actually defaults to :table_id DESC.

				List of available where parameters, and what they map to. Technically, more are supported, but these should be the preferred items moving forward.

				'$lt'         => '<'
				'$lte'        => '<='
				'$gt'         => '>'
				'$gte'        => '>='
				'$eq'         => '='
				'$neq'        => '!='
				'$is'         => 'IS' // only works with nulls
				'$isnot'      => 'IS NOT' // only works with nulls
				'$like'       => 'LIKE'
				'$notlike'    => 'NOT LIKE'
				'$lk'         => 'LIKE'
				'$nlk'        => 'NOT LIKE'
				'$in'         => 'IN'
				'$has'        => 'LIKE' // special like extension, surrounds a string in % signs automatically
				*/

				// https://vm122.clubspeedtiming.com/api/index.php/logs?key=cs-dev&where={"terminal": "ClubSpeed PHP API", "message": {"$like": "ERROR%"}}&limit=3&offset=10&order=logsId DESC

				self::initialize();

				$urlParams = array(
					'limit'  => isset($params['limit'])  ? $params['limit']  : null,
					'page'   => isset($params['page'])   ? $params['page']   : null,
					'offset' => isset($params['offset']) ? $params['offset'] : null,
					'where'  => isset($params['where'])  ? json_encode($params['where']) : null, // {"terminal": "ClubSpeed PHP API", "message": {"$like": "ERROR%"}}
					'order'  => isset($params['order'])  ? $params['order']  : null,
					'key'    => self::$privateKey
					);

				// Remove empty items
				foreach($urlParams as $key => $value) {
				    if(empty($value)) {
				        unset($urlParams[$key]);
				    }
				}

        $url = self::$apiURL . "/logs.json?" . http_build_query($urlParams);

        $result = self::call($url);

				return isset($result['error']) ? $result : $result['response']->body;
		}

    public static function getDataTableData($params)
    {
        set_time_limit(66);

				self::initialize();

				// Handle searching
				if(!empty($params['search']['value'])) $params['where']['message'] = array('$like' => '%' . $params['search']['value'] . '%');

				// Handle sorting
				$formattedOrder = array();
				foreach($params['order'] as $key => $order) {
						$direction = $order['dir'] == 'asc' ? 'ASC' : 'DESC';
						$columnName = $params['columns'][$order['column']]['name'];
						$formattedOrder[] = "{$columnName} {$direction}";
				}
				$formattedOrder = implode(',', $formattedOrder);

				// Build params for API call
				$urlParams = array(
					'limit'  => isset($params['length']) && $params['length'] !== -1 ? $params['length']  : null,
					'offset' => isset($params['start'])  ? $params['start'] : null,
					'where'  => isset($params['where'])  ? json_encode($params['where']) : null,
					'order'  => !empty($formattedOrder)  ? $formattedOrder  : null,
					'key'    => self::$privateKey
				);

				// Remove empty items
				foreach($urlParams as $key => $value) {
				    if(empty($value)) {
				        unset($urlParams[$key]);
				    }
				}

        $terminalQuery = '';
        if (isset($params['where']) && isset($params['where']['terminal']) && !empty($params['where']['terminal'])){
          $terminalQuery = 'where=' . urlencode(json_encode(array('terminal' => $params['where']['terminal'])));
        }

        $recordsTotalPath = self::$apiURL . "/{$params['model']}/count?" . $terminalQuery . "&key=" . self::$privateKey;
        $urlRecordsTotal = self::call($recordsTotalPath);
        $urlData = self::call(self::$apiURL . "/{$params['model']}?" . http_build_query($urlParams));
        $urlRecordsFiltered = self::call(self::$apiURL . "/{$params['model']}/count?" . $terminalQuery . "&key=" . http_build_query($urlParams));

				$data = array();
				foreach($urlData['response']->body as $key => $row) {
					foreach($row as $item) {
						$data[$key][] = $item;
					}
				}

				$dataSet = array(
					'draw' => $params['draw'],
					'recordsTotal' => $urlRecordsTotal['response']->body,
					'recordsFiltered' => $urlRecordsFiltered['response']->body,
					'data' => $data
					//'apiParams' => $urlParams // Debug
				);
				return $dataSet;

		}

    public static function insertTranslationsBatch($newTranslations)
    {
        self::initialize();

        $url = self::$apiURL . '/translations/batch?key=' . self::$privateKey;

        $result = self::call($url,$newTranslations,'POST');
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null)
        {
            if (isset($response->body->translations))
            {
                return $response->body->translations;
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

    public static function getCurrentCultureForOnlineBooking()
    {
        self::initialize();

        $url = self::$apiURL . '/settings/get.json?group=Booking&setting=currentCulture&key=' . self::$privateKey;

        $result = self::call($url);
        $result = $result['response'];

        if ($result !== null && isset($result->body->settings->currentCulture))
        {
            return $result->body->settings->currentCulture->SettingValue;
        }
        else
        {
            return 'en-US';
        }

    }

    public static function getCurrentCultureForMobile()
    {
        self::initialize();

        $url = self::$apiURL . '/settings/get.json?group=MobileApp&setting=currentCulture&key=' . self::$privateKey;

        $result = self::call($url);
        $result = $result['response'];

        if ($result !== null && isset($result->body->settings->currentCulture))
        {
            return $result->body->settings->currentCulture->SettingValue;
        }
        else
        {
            return 'en-US';
        }

    }

    public static function getSpeedLevels()
    {
        self::initialize();

        $result = self::getJSON('speedlevel'); //'/tracks/index.json?&key=

        if (isset($result))
        {
            $formattedSpeedLevels = array('all' => 'All');
            foreach($result as $currentSpeedLevel)
            {
                if (!$currentSpeedLevel->deleted)
                {
                    $formattedSpeedLevels[$currentSpeedLevel->speedLevel] = $currentSpeedLevel->description;
                }
            }
            return $formattedSpeedLevels;
        }
        else
        {
            return array('all' => 'All');
        }
    }


    public static function updateGiftCardBalances($params)
    {
        self::initialize();

        $userId = 1; //Hard-coded for now
        $params['userId'] = $userId;

        $url = self::$apiURL . '/giftcardbalance/register?key=' . self::$privateKey;

        $result = self::call($url,$params,'POST');
        $response = $result['response'];
        $error = $result['error'];

        if ($response !== null)
        {
            if (isset($response->code) && $response->code == 200)
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
            return false;
        }
    }

    public static function getGiftCardBalances($listOfGiftCards)
    {
        self::initialize();
        $params = array('cards' => $listOfGiftCards);
        return self::getJSON("reports/gift_card_balance.json", $params);
    }

    public static function getGiftCardTransactions($listOfGiftCards)
    {
        self::initialize();
        $params = array('cards' => $listOfGiftCards);
        return self::getJSON("reports/gift_card_transactions.json", $params);
    }

    public static function getGiftCardProducts()
    {
        self::initialize();
        $params = array(
                'productType' => 7, //In Club Speed, type 7 is the Gift Card type
                'select' => 'productId,description,price1,enabled',
                'deleted' => 0,
                'enabled' => 1
            );
        return self::getJSON("products.json", $params);
    }

    public static function doesServerHaveEurekas()
    {
        self::initialize();
        $result = self::getJSON("version/eurekas.json");
        $serverHasEurekas = ($result !== null);
        return $serverHasEurekas;
    }

		public static function getCountries($params = null)
    {
        self::initialize();
        $result = self::getJSON("countries.json");
        return $result;
    }

    public static function getCustomerStatus()
    {
        self::initialize();
        return self::getJSON('customerstatus');
    }
}
