<?php

require_once(app_path().'/includes/includes.php');

class RegistrationController extends BaseController
{

    public function settings()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

        $registrationSettings = CS_API::getSettingsFor('Registration');

        if ($registrationSettings === null)
        {
            return Redirect::to('/disconnected');
        }
        $registrationSettingsCheckedData = array();
        $registrationSettingsData = array();
        foreach($registrationSettings->settings as $setting)
        {
            $registrationSettingsCheckedData[$setting->SettingName] = ($setting->SettingValue ? 'checked' : '');
            $registrationSettingsData[$setting->SettingName] = $setting->SettingValue;
        }

        Session::put('registrationSettings',$registrationSettingsData);

        return View::make('/screens/registration/settings',
            array('controller' => 'RegistrationController',
                  'isChecked' => $registrationSettingsCheckedData,
                  'registrationSettings' => $registrationSettingsData
            ));
    }

    public function updateSettings()
    {
        $input = Input::all();

        //Begin formatting form input for processing
        $newSettings = array();
        $newSettings['genderRequired'] = isset($input['genderRequired']) ? 1 : 0;
        $newSettings['genderShown'] = isset($input['genderShown']) ? 1 : 0;
        //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('registrationSettings',array());
        foreach($currentSettings as $currentSettingName => $currentSettingValue)
        {
            if (isset($newSettings[$currentSettingName]))
            {
                if ($newSettings[$currentSettingName] == $currentSettingValue) //If the setting hasn't changed
                {
                    unset($newSettings[$currentSettingName]); //Remove it from the list of new settings
                }
            }
        }

        $result = CS_API::updateSettingsFor('Registration',$newSettings);

        if ($result === false)
        {
            return Redirect::to('registration/settings')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('registration/settings')->with( array('message' => 'Settings updated successfully!'));
    }
}