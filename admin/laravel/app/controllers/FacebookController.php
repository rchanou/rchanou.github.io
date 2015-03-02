<?php

use Illuminate\Http\Response;
require_once(app_path().'/includes/includes.php');

class FacebookController extends BaseController
{
		public function logs()
    {
				return View::make('/screens/facebook/logs', array(
            'controller' => 'FacebookController'
        ));
    }

		public function logEntries()
    {
        $params = Input::all();
				$logEntries = CS_API::getLogs($params);

				// Build response, either JSON or JSONP based upon existance of "callback" GET parameter
				$callback = Input::get('callback');
				$content = isset($callback) ? $callback . '(' . json_encode($logEntries) . ')' : json_encode($logEntries);
				$response = new Response();
        $response->header('Content-Type', 'application/json')->setContent($content, 200);

        return $response;
    }

    public function afterRaceSettings()
    {
        $afterRaceSettings = CS_API::getSettingsFromNewTableFor('FacebookAfterRace');
        if ($afterRaceSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $afterRaceSettingsCheckedData = array();
        $afterRaceSettingsData = array();
        $afterRaceSettingsIds = array();
        foreach($afterRaceSettings->settings as $setting)
        {
            $afterRaceSettingsCheckedData[$setting->name] = ($setting->value ? 'checked' : '');
            $afterRaceSettingsData[$setting->name] = $setting->value;
            $afterRaceSettingsIds[$setting->name] = $setting->settingsId;
        }

				Session::put('afterRaceSettings', $afterRaceSettingsData);
        Session::put('afterRaceSettingsIds', $afterRaceSettingsIds);

        return View::make('/screens/facebook/afterRaceSettings',
            array('controller' => 'FacebookController',
                'isChecked' => $afterRaceSettingsCheckedData,
                'afterRaceSettings' => $afterRaceSettingsData,
                'user' => strtolower(Session::get('user'))
            ));
    }

    public function updateAfterRaceSettings()
    {
        $input = Input::all();

        //Begin formatting form input for processing - defaults available for any missing settings
        $newSettings = array();
        $newSettings['featureIsEnabled'] = isset($input['featureIsEnabled']) ? 1 : 0;
        $newSettings['postingIsEnabled'] = isset($input['postingIsEnabled']) ? 1 : 0;
        $newSettings['link'] = isset($input['link']) ? $input['link'] : '';
        $newSettings['message'] = isset($input['message']) ? $input['message'] : '';
        $newSettings['photoUrl'] = isset($input['photoUrl']) ? $input['photoUrl'] : '';
        $newSettings['name'] = isset($input['name']) ? $input['name'] : '';
        $newSettings['description'] = isset($input['description']) ? $input['description'] : '';
        $newSettings['caption'] = isset($input['caption']) ? $input['caption'] : '';
        //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('afterRaceSettings',array());
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

        $afterRaceSettingsIds = Session::get('afterRaceSettingsIds',array());
        $result = CS_API::updateSettingsInNewTableFor('FacebookAfterRace',$newSettings,$afterRaceSettingsIds);

        if ($result === false)
        {
            return Redirect::to('facebook/after-race-settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('facebook/after-race-settings')->with( array('message' => 'Settings updated successfully!'));
    }

		public function data()
		{
			$params = Input::get();
			$params['model'] = 'logs';
			$params['where']['terminal'] = 'Facebook';

			$data = CS_API::getDataTableData($params);

			return $data;
		}
}
