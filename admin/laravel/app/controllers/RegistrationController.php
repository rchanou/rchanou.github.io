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

        //$mainEngineSettings = CS_API::getSettingsFor('MainEngine');
        $mainEngineSettings = new stdClass();
        $mainEngineSettings->settings = new stdClass();
        $mainEngineSettingNames = array('Reg_EnableFacebook', 'Reg_CaptureProfilePic', 'AgeNeedParentWaiver', 'AgeAllowOnlineReg', 'FacebookPageURL');
        foreach($mainEngineSettingNames as $settingName){
          $setting = CS_API::getJSON("settings/get", array('group' => 'MainEngine', 'setting' => $settingName))->settings->$settingName;
          $mainEngineSettings->settings->$settingName = $setting;
        }

        $reg1Settings = CS_API::getSettingsFor('Registration1');

        if ($registrationSettings === null || $mainEngineSettings === null || $reg1Settings === null)
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

        $mainEngineSettingsData = array();
        foreach($mainEngineSettings->settings as $setting)
        {
          $registrationSettingsCheckedData[$setting->SettingName] = ($setting->SettingValue ? 'checked' : '');
          $mainEngineSettingsData[$setting->SettingName] = $setting->SettingValue;
        }
        Session::put('mainEngineSettings',$mainEngineSettingsData);

        $reg1SettingsData = array();
        foreach($reg1Settings->settings as $setting)
        {
          $registrationSettingsCheckedData[$setting->SettingName] = ($setting->SettingValue ? 'checked' : '');
          $reg1SettingsData[$setting->SettingName] = $setting->SettingValue;
        }
        Session::put('reg1Settings', $reg1SettingsData);

        return View::make('/screens/registration/settings',
            array('controller' => 'RegistrationController',
                  'isChecked' => $registrationSettingsCheckedData,
                  'registrationSettings' => $registrationSettingsData,
                  'mainEngineSettings' => $mainEngineSettingsData
            ));
    }

    public function updateSettings()
    {
        $input = Input::all();

        //Begin formatting form input for processing
        $newRegistrationSettings = array();
        $newRegistrationSettings['genderRequired'] = isset($input['genderRequired']) ? 1 : 0;
        $newRegistrationSettings['genderShown'] = isset($input['genderShown']) ? 1 : 0;
        $newRegistrationSettings['cfgRegAllowMinorToSign'] = isset($input['cfgRegAllowMinorToSign']) ? 1 : 0;
        $newRegistrationSettings['CfgRegDisblEmlForMinr'] = isset($input['CfgRegDisblEmlForMinr']) ? 1 : 0;
        $newRegistrationSettings['CfgRegUseMsign'] = isset($input['CfgRegUseMsign']) ? 1 : 0;
        $newRegistrationSettings['showTextingWaiver'] = isset($input['showTextingWaiver']) ? 1 : 0;
        $newRegistrationSettings['defaultCountry'] = isset($input['defaultCountry']) ? $input['defaultCountry'] : '';
        $newRegistrationSettings['emailText'] = isset($input['emailText']) ? $input['emailText'] : '';
        $newRegistrationSettings['textingWaiver'] = isset($input['textingWaiver']) ? $input['textingWaiver'] : '';

        $newMainEngineSettings = array();
        $newMainEngineSettings['Reg_EnableFacebook'] = isset($input['Reg_EnableFacebook']) ? 1 : 0;
        $newMainEngineSettings['Reg_CaptureProfilePic'] = isset($input['Reg_CaptureProfilePic']) ? 1 : 0;
        $newMainEngineSettings['AgeAllowOnlineReg'] = isset($input['AgeAllowOnlineReg']) ? $input['AgeAllowOnlineReg'] : '';
        $newMainEngineSettings['AgeNeedParentWaiver'] = isset($input['AgeNeedParentWaiver']) ? $input['AgeNeedParentWaiver'] : '';
        $newMainEngineSettings['FacebookPageURL'] = isset($input['FacebookPageURL']) ? $input['FacebookPageURL'] : '';

        $newreg1Settings = array();
        $newreg1Settings['enableWaiverStep'] = isset($input['enableWaiverStep']) ? 1 : 0;


        //End formatting

        //Identify the settings that actually changed and need to be sent to Club Speed
        $currentSettings = Session::get('registrationSettings',array());
        foreach($currentSettings as $currentSettingName => $currentSettingValue)
        {
            if (isset($newRegistrationSettings[$currentSettingName]))
            {
                if ($newRegistrationSettings[$currentSettingName] == $currentSettingValue) //If the setting hasn't changed
                {
                    unset($newRegistrationSettings[$currentSettingName]); //Remove it from the list of new settings
                }
            }
        }

        $currentMainEngineSettings = Session::get('mainEngineSettings', array());
        foreach($currentMainEngineSettings as $currentSettingName => $currentSettingvalue)
        {
          if (isset($newMainEngineSettings[$currentSettingName]))
          {
            if ($newMainEngineSettings[$currentSettingName] == $currentSettingvalue)
            {
              unset($newMainEngineSettings[$currentSettingName]);
            }
          }
        }

        $currentreg1Settings = Session::get('reg1Settings', array());
        foreach($currentreg1Settings as $currentSettingName => $currentSettingvalue)
        {
          if (isset($newreg1Settings[$currentSettingName]))
          {
            if ($newreg1Settings[$currentSettingName] == $currentSettingvalue)
            {
              unset($newreg1Settings[$currentSettingName]);
            }
          }
        }

        $result = CS_API::updateSettingsFor('Registration',$newRegistrationSettings) && CS_API::updateSettingsFor('MainEngine',$newMainEngineSettings)&& CS_API::updateSettingsFor('Registration1',$newreg1Settings);

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
