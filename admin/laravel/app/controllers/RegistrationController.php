<?php

require_once(app_path().'/includes/includes.php');

class RegistrationController extends BaseController
{
    public function settings()
    {
        $registrationSettings = CS_API::getSettingsFor('Registration');

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

        $customerFields = array(
          array('shownId' => 'genderShown', 'requiredId' => 'genderRequired', 'label' => 'Gender'),
          array('shownId' => 'CfgRegAddShow', 'requiredId' => 'CfgRegAddReq', 'label' => 'Address'),
          array('shownId' => 'CfgRegCityShow', 'requiredId' => 'CfgRegCityReq', 'label' => 'City'),
          array('shownId' => 'CfgRegStateShow', 'requiredId' => 'CfgRegStateReq', 'label' => 'State'),
          array('shownId' => 'CfgRegZipShow', 'requiredId' => 'CfgRegZipReq', 'label' => 'Zip'),
          array('shownId' => 'CfgRegCntryShow', 'requiredId' => 'CfgRegCntryReq', 'label' => 'Country'),
          array('shownId' => 'CfgRegRcrNameShow', 'requiredId' => 'CfgRegRcrNameReq', 'label' => 'Racer Name'),
          array('shownId' => 'CfgRegSrcShow', 'requiredId' => 'CfgRegSrcReq', 'label' => 'How did you hear about us?'),
          array('shownId' => 'CfgRegDrvrLicShow', 'requiredId' => 'CfgRegDrvrLicReq', 'label' => 'Driver\'s License', 'secondColumn' => true),
          array('shownId' => 'CfgRegPhoneShow', 'requiredId' => 'CfgRegPhoneReq', 'label' => 'Phone', 'secondColumn' => true),
          array('shownId' => 'CfgRegEmailShow', 'requiredId' => 'CfgRegEmailReq', 'label' => 'E-mail', 'secondColumn' => true),
          array('shownId' => 'cfgRegCustTxt1Show', 'requiredId' => 'cfgRegCustTxt1req', 'label' => 'Custom Text 1', 'secondColumn' => true),
          array('shownId' => 'cfgRegCustTxt2Show', 'requiredId' => 'cfgRegCustTxt2req', 'label' => 'Custom Text 2', 'secondColumn' => true),
          array('shownId' => 'cfgRegCustTxt3Show', 'requiredId' => 'cfgRegCustTxt3req', 'label' => 'Custom Text 3', 'secondColumn' => true),
          array('shownId' => 'cfgRegCustTxt4Show', 'requiredId' => 'cfgRegCustTxt4req', 'label' => 'Custom Text 4', 'secondColumn' => true)
        );


        return View::make('/screens/registration/settings',
            array('controller' => 'RegistrationController',
                  'customerFields' => $customerFields,
                  'isChecked' => $registrationSettingsCheckedData,
                  'registrationSettings' => $registrationSettingsData,
                  'mainEngineSettings' => $mainEngineSettingsData
            ));
    }

    public function updateSettings()
    {
        $input = Input::all();

        //Begin formatting form input for processing
        $newRegSettings = array(
          'cfgRegAllowMinorToSign',
          'CfgRegDisblEmlForMinr',
          'CfgRegUseMsign',
          'CfgRegAddShow',
          'CfgRegAddReq',
          'CfgRegCityShow',
          'CfgRegCityReq',
          'CfgRegStateShow',
          'CfgRegStateReq',
          'CfgRegZipShow',
          'CfgRegZipReq',
          'CfgRegCntryShow',
          'CfgRegCntryReq',
          'CfgRegRcrNameShow',
          'CfgRegRcrNameReq',
          'CfgRegSrcShow',
          'CfgRegSrcReq',
          'cfgRegCustTxt1Show',
          'cfgRegCustTxt1req',
          'cfgRegCustTxt2Show',
          'cfgRegCustTxt2req',
          'cfgRegCustTxt3Show',
          'cfgRegCustTxt3req',
          'cfgRegCustTxt4Show',
          'cfgRegCustTxt4req',
          'CfgRegDrvrLicShow',
          'CfgRegDrvrLicReq',
          'CfgRegPhoneShow',
          'CfgRegPhoneReq',
          'CfgRegEmailShow',
          'CfgRegEmailReq',
          'genderRequired',
          'genderShown',
          'cfgRegAllowMinorToSign',
          'CfgRegDisblEmlForMinr',
          'CfgRegUseMsign',
          'showTextingWaiver'
        );

        $newRegistrationSettings = array();

        foreach ($newRegSettings as $settingName){
          $newRegistrationSettings[$settingName] = isset($input[$settingName]) ? 1 : 0;
        }

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

    public function translations()
    {
        $supportedCultures = array(
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'en-NZ' => 'English (NZ)',
            'en-AU' => 'English (AU)',
            'en-IE' => 'English (IE)',
            'en-CA' => 'English (CA)',
            'es-MX' => 'Español',
            'es-CR' => 'Español (CR)',
            'es-ES' => 'Castellano',
            'es-PR' => 'Español (PR)',
            'ru-RU' => 'Pусский язык',
            'fr-CA' => 'Français',
            'de-DE' => 'Deutsch',
            'nl-NL' => 'Nederlands',
            'pl-PL' => 'Język polski',
            'da-DK' => 'Dansk',
            'ar-AE' => 'العربية',
            'it-IT' => 'Italiano',
            'bg-BG' => 'български език',
            'sv-SE' => 'Svenska'
        );

        $registrationCultureSetting = CS_API::getSettingsFromNewTableFor('Registration','currentCulture');
        $currentCulture = isset($registrationCultureSetting->settings[0]->value) ? $registrationCultureSetting->settings[0]->value : 'en-US';

        $translations = CS_API::getTranslations('Registration');

        return View::make('/screens/registration/translations',
            array('controller' => 'RegistrationController',
                'supportedCultures' => $supportedCultures,
                'currentCulture' => $currentCulture,
                'translations' => $translations
            )
        );
    }

    public function updateTranslations()
    {
        $input = Input::all();
        unset($input['_token']); //Removing Laravel's default form value
        $cultureKey = $input['cultureKey'];
        unset($input['cultureKey']);

        $input = $input['trans']; //HACK: PHP converts periods to underscores in _GET and _POST. Wrapping input names in an array gets around this behavior.

        //Format the missing string data as expected by Club Speed's API
        $updatedTranslations = array(); //Destined to a PUT
        $newTranslations = array(); //Destined to a POST
        foreach($input as $stringId => $stringValue)
        {
            if (isset($stringId))
            {
                if (!$this->contains($stringId,'new_'))
                {
                    $updatedTranslations[] = array(
                        'translationsId' => str_replace("id_","",$stringId),
                        'value' => $stringValue
                    );
                }
                else if ($stringValue != "")
                {
                    $newTranslations[] = array(
                        'name' => str_replace("id_new_","",$stringId),
                        'namespace' => 'Registration',
                        'value' => $stringValue,
                        'defaultValue' => $stringValue,
                        'culture' => $cultureKey,
                        'comment' => '');
                }
            }
        }

        $result = null;
        if (count($updatedTranslations) > 0)
        {
            $result = CS_API::updateTranslationsBatch($updatedTranslations);

            $updateWasSuccessful = ($result !== null);
            if ($updateWasSuccessful === false)
            {
                return Redirect::to('registration/translations')->with( array('error' => 'One or more translations could not be updated. Please try again.'));
            }
            else if ($updateWasSuccessful === null)
            {
                return Redirect::to('/disconnected');
            }
        }

        $result = null;
        if (count($newTranslations) > 0)
        {
            $result = CS_API::insertTranslationsBatch($newTranslations);

            $insertWasSuccessful = ($result !== null);
            if ($insertWasSuccessful === false)
            {
                return Redirect::to('registration/translations')->with( array('error' => 'One or more translations could not be created. Please try again.'));
            }
            else if ($insertWasSuccessful === null)
            {
                return Redirect::to('/disconnected');
            }
        }

        //Standard success message
        return Redirect::to('registration/translations')->with( array('message' => 'Translations updated successfully!'));

    }

    public function updateCulture($cultureKey)
    {
        $supportedCultures = array(
            'en-US',
            'en-GB',
            'en-NZ',
            'en-AU',
            'en-IE',
            'en-CA',
            'es-MX',
            'es-CR',
            'es-ES',
            'es-PR',
            'ru-RU',
            'fr-CA',
            'de-DE',
            'nl-NL',
            'pl-PL',
            'da-DK',
            'ar-AE',
            'it-IT',
            'bg-BG',
            'sv-SE'
        );


        if (!in_array($cultureKey,$supportedCultures))
        {
            return Redirect::to('registration/translations')->with( array('error' => 'The desired culture was not recognized and could not be updated. Please contact Club Speed Support.'));
        }

        $registrationCultureSetting = CS_API::getSettingsFromNewTableFor('Registration','currentCulture');
        $currentCultureSettingID = isset($registrationCultureSetting->settings[0]->settingsId) ? $registrationCultureSetting->settings[0]->settingsId : null;
        if ($currentCultureSettingID === null)
        {
            return Redirect::to('registration/translations')->with( array('error' => 'The current culture could not be updated. Please try again later. If the issue persists, contact Club Speed support.'));
        }

        $result = CS_API::updateSettingsInNewTableFor('Registration',array('currentCulture' => $cultureKey), array('currentCulture' => $currentCultureSettingID));
        if ($result != true)
        {
            return Redirect::to('registration/translations')->with( array('error' => 'The current culture could not be updated. Please try again later. If the issue persists, contact Club Speed support.'));
        }

        //Standard success message
        return Redirect::to('registration/translations')->with( array('message' => 'Current culture updated successfully!'));
    }

    private static function contains(&$haystack, $needle)
    {
        $result = strpos($haystack, $needle);
        return $result !== false;
    }
}
