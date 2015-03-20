<?php

require_once(app_path().'/includes/includes.php');

class SpeedTextController extends BaseController
{
		public function logs()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }
        return View::make('/screens/speedtext/logs',array(
            'controller' => 'SpeedTextController'
        ));
    }

    public function settings()
    {
				$session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

        $response = CS_API::getSettingsFromNewTableFor('SpeedText');
        if ($response === null)
        {
            return Redirect::to('/disconnected');
        }

				$providerOptionDetailSets = array(
					'twilio' => array(
						array('key' => 'sid', 'label' => 'API User', 'type' => 'text', 'tip' => 'The Account SID provided by Twilio.'),
						array('key' => 'token', 'label' => 'API Key', 'type' => 'text', 'tip' => 'The Auth Token provided by Twilio.'),
						array('key' => 'from', 'label' => 'From', 'type' => 'text', 'tip' => 'The phone number(s), provided by Twilio, that the text messages will be sent from. Typically in the format "+12223334444".')
					),
					'bulksms' => array(
						array('key' => 'username', 'label' => 'Username', 'type' => 'text', 'tip' => 'The username for your BulkSMS account.'),
						array('key' => 'password', 'label' => 'Password', 'type' => 'text', 'tip' => 'The password for your BulkSMS account')
					)
				);

				$settingsCheckedData = array();
				$settingsData = array();
				$settingsIds = array();

				// get provider
				foreach($response->settings as $setting){
					if ($setting->name === 'provider'){
						$provider = $setting->value ?: $setting->default;
						$providerOptionDetails = $providerOptionDetailSets[$provider];
						break;
					}
				}

				foreach($response->settings as $setting)
				{
					if($setting->name === 'providerOptions'){
						$providerOptions = json_decode($setting->value);
						foreach ($providerOptionDetails as $index => $details){
							$optionKey = $details['key'];
							$settingsData[$optionKey] = isset($providerOptions->$optionKey) ? $providerOptions->$optionKey : "";
							if ($index < count($providerOptionDetails) / 2){
								$firstColumnProviderOptions[] = $optionKey;
							} else {
								$secondColumnProviderOptions[] = $optionKey;
							}
						}
					} else if ($setting->name === 'from'){
						$from = json_decode($setting->value);
						$settingsData['from'] = implode(",", $from);
					} else {
						$settingsCheckedData[$setting->name] = ($setting->value ? 'checked' : '');
						$settingsData[$setting->name] = $setting->value;
					}
					$settingsIds[$setting->name] = $setting->settingsId;
				}

				Session::put('settings', $settingsData);
        Session::put('settingsIds', $settingsIds);
				Session::put('providerOptionDetails', $providerOptionDetails);

        $supportedProviders = array('twilio' => 'Twilio', 'bulksms' => 'BulkSMS');

        return View::make('/screens/speedtext/settings',
            array('controller' => 'SpeedTextController',
                'isChecked' => $settingsCheckedData,
                'settings' => $settingsData,
                'supportedProviders' => $supportedProviders,
								'firstColumnProviderOptions' => $firstColumnProviderOptions,
								'secondColumnProviderOptions' => $secondColumnProviderOptions,
								'providerOptionDetails' => $providerOptionDetails,
                'user' => strtolower(Session::get('user'))
            ));
    }

    public function updateSettings()
    {

        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

        $input = Input::all();

				$currentSettings = Session::get('settings',array());

        //Begin formatting form input for processing - defaults available for any missing settings
        $newSettings = array();

				$providerOptions = array();
				$providerOptionDetails = Session::get('providerOptionDetails', array());
				//var_dump($providerOptionDetails, $providerOptions, $input); die();
				foreach ($providerOptionDetails as $details){
					$providerOptions[$details['key']] = $input[$details['key']];
				}
				$providerOptionsJson = json_encode($providerOptions);
				$newSettings['providerOptions'] = $providerOptionsJson;

				if(isset($input['from'])){
					$from = explode(",", $input['from']);
					$json = json_encode($from);
					$newSettings['from'] = $json;
				}

				$isSupport = strtolower(Session::get('user')) === 'support';
				if ($isSupport){
					$newSettings['isEnabled'] = isset($input['isEnabled']) ? 1 : 0;
				}

				$newSettings['provider'] = isset($input['provider']) ? $input['provider'] : '';
				$newSettings['cutoffHour'] = isset($input['cutoffHour']) ? $input['cutoffHour'] : 4;
        $newSettings['textingIsEnabled'] = isset($input['textingIsEnabled']) ? 1 : 0;
        $newSettings['heatsPriorToSend'] = isset($input['heatsPriorToSend']) ? $input['heatsPriorToSend'] : 3;
        $newSettings['message'] = isset($input['message']) ? $input['message'] : '';
        //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        foreach($currentSettings as $currentSettingName => $currentSettingValue)
        {
            // TODO, could add additional security to filter the featureIsEnabled by "support" user here $session['user']);
            if (isset($newSettings[$currentSettingName]))
            {
                if ($newSettings[$currentSettingName] == $currentSettingValue) //If the setting hasn't changed
                {
                    unset($newSettings[$currentSettingName]); //Remove it from the list of new settings
                }
            }
        }

        $settingsIds = Session::get('settingsIds',array());
        $result = CS_API::updateSettingsInNewTableFor('SpeedText',$newSettings,$settingsIds);

        if ($result === false)
        {
            return Redirect::to('speedtext/settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('speedtext/settings')->with( array('message' => 'Settings updated successfully!'));
    }

		public function data()
		{
			$params = Input::get();
			$params['model'] = 'logs';
			$params['where']['terminal'] = 'SpeedText';

			$data = CS_API::getDataTableData($params);

			return $data;
		}
}
