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

        $settings = CS_API::getSettingsFromNewTableFor('SpeedText');
        if ($settings === null)
        {
            return Redirect::to('/disconnected');
        }
        $settingsCheckedData = array();
        $settingsData = array();
        $settingsIds = array();
        foreach($settings->settings as $setting)
        {
            if($setting->name === 'providerOptions'){
              $providerOptions = json_decode($setting->value);
              $settingsData['sid'] = isset($providerOptions->sid) ? $providerOptions->sid : "";
              $settingsData['token'] = isset($providerOptions->token) ? $providerOptions->token : "";
              $settingsIds[$setting->name] = $setting->settingsId;
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

        $supportedProviders = array('twilio' => 'Twilio');

        return View::make('/screens/speedtext/settings',
            array('controller' => 'SpeedTextController',
                'isChecked' => $settingsCheckedData,
                'settings' => $settingsData,
                'supportedProviders' => $supportedProviders,
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

        //Begin formatting form input for processing - defaults available for any missing settings
        $newSettings = array();

        $providerOptions = array(
          'sid' => $input['sid'],
          'token' => $input['token']
        );
        $providerOptionsJson = json_encode($providerOptions);
        $newSettings['providerOptions'] = $providerOptionsJson;

        if(isset($input['from'])){
          $from = explode(",", $input['from']);
          $json = json_encode($from);
          $newSettings['from'] = $json;
          echo $json;
        }

        $newSettings['isEnabled'] = isset($input['isEnabled']) ? 1 : 0;
        $newSettings['textingIsEnabled'] = isset($input['textingIsEnabled']) ? 1 : 0;
        $newSettings['heatsPriorToSend'] = isset($input['heatsPriorToSend']) ? $input['heatsPriorToSend'] : 0;
        $newSettings['message'] = isset($input['message']) ? $input['message'] : '';
        //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('settings',array());
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
