<?php

require_once(app_path().'/includes/includes.php');

class RegistrationController extends BaseController
{
    public $image_directory;
    public $image_filenames;
    public $image_paths;
    public $image_urls;

    public function __construct()
    {
        //Image uploader data
        $this->image_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cs-registration' . DIRECTORY_SEPARATOR . 'images';
        $this->image_filenames = array('bg_default.jpg','default_header.png');
        $this->image_paths = array();
        $this->image_urls = array();

        foreach($this->image_filenames as $currentFileName)
        {
            $this->image_paths[$currentFileName] = $this->image_directory . DIRECTORY_SEPARATOR . $currentFileName;
            $this->image_urls[$currentFileName] = '/assets/cs-registration/images/' . $currentFileName;
        }
    }

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
          array('shownId' => 'CfgRegZipShow', 'requiredId' => 'CfgRegZipReq', 'label' => 'Zip', 'validatedId' => 'zipValidated'),
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
                  'mainEngineSettings' => $mainEngineSettingsData,
                  'countries' => $this->countries,
                'background_image_url' => is_file($this->image_paths['bg_default.jpg']) ? $this->image_urls['bg_default.jpg'] : null,
                'header_image_url' => is_file($this->image_paths['default_header.png']) ? $this->image_urls['default_header.png'] : null
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
          'showTextingWaiver',
          'zipValidated',
          'cfgRegShowBeenHereBefr',
          'CfgRegValidateGrp',
          'minorSignatureWithParent'
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
            'fr-FR' => 'Français',
            'fr-CA' => 'Français (CA)',
            'de-DE' => 'Deutsch',
            'nl-NL' => 'Nederlands',
            'pl-PL' => 'Język polski',
            'da-DK' => 'Dansk',
            'ar-AE' => 'العربية',
            'it-IT' => 'Italiano',
            'bg-BG' => 'български език',
            'sv-SE' => 'Svenska',
            'zh-CN' => '中文'
        );

        $registrationCultureSetting = CS_API::getSettingsFromNewTableFor('Registration','currentCulture');
        $currentCulture = isset($registrationCultureSetting->settings[0]->value) ? $registrationCultureSetting->settings[0]->value : 'en-US';

        $enabledCulturesSetting = CS_API::getSettingsFromNewTableFor('Registration','enabledCultures');
        $enabledCulturesSetting = isset($enabledCulturesSetting->settings[0]->value) ? $enabledCulturesSetting->settings[0]->value : null;
        $enabledCultures = null;
        if ($enabledCulturesSetting != null)
        {
            $enabledCultures = array();
            $enabledCulturesSetting = json_decode($enabledCulturesSetting);
            foreach($enabledCulturesSetting as $key => $culture)
            {
                $enabledCultures[$culture] = $culture;
            }
        }

        $translations = CS_API::getTranslations('Registration');

        return View::make('/screens/registration/translations',
            array('controller' => 'RegistrationController',
                'supportedCultures' => $supportedCultures,
                'supportedCulturesSplit' => array_chunk($supportedCultures,ceil(count($supportedCultures)/3),true),
                'currentCulture' => $currentCulture,
                'enabledCultures' => $enabledCultures,
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
            'fr-FR',
            'de-DE',
            'nl-NL',
            'pl-PL',
            'da-DK',
            'ar-AE',
            'it-IT',
            'bg-BG',
            'sv-SE',
            'zh-CN'
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

    public function updateDropdownLanguages()
    {
        $input = Input::all();
        unset($input["_token"]);

        if ($input !== null)
        {
            $enabledCultures = json_encode(array_keys($input));
            $enabledCulturesSetting = CS_API::getSettingsFromNewTableFor('Registration','enabledCultures');
            $enabledCulturesSettingID = isset($enabledCulturesSetting->settings[0]->settingsId) ? $enabledCulturesSetting->settings[0]->settingsId : null;
            if ($enabledCulturesSettingID === null)
            {
                return Redirect::to('registration/translations')->with( array('error' => 'Enabled cultures could not be updated. Please try again later. If the issue persists, contact Club Speed support.'));
            }

            $result = CS_API::updateSettingsInNewTableFor('Registration',array('enabledCultures' => $enabledCultures), array('enabledCultures' => $enabledCulturesSettingID));
            if ($result != true)
            {
                return Redirect::to('registration/translations')->with( array('error' => 'Enabled cultures could not be updated. Please try again later. If the issue persists, contact Club Speed support.'));
            }
        }

        //Standard success message
        return Redirect::to('registration/translations')->with( array('message' => 'Dropdown languages updated successfully!'));
    }

    public function updateImage()
    {
        // Build the input for our validation
        $input = array('image' => Input::file('image'));
        $filename = Input::get('filename');

        // Within the ruleset, make sure we let the validator know that this
        $rules = array(
            'image' => 'required|max:10000',
        );

        // Now pass the input and rules into the validator
        $validator = Validator::make($input, $rules);

        // Check to see if validation fails or passes
        if ($validator->fails()) {
            // VALIDATION FAILED
            return Redirect::to('registration/settings')->with('error', 'The provided file was not an image');
        } else {
            // SAVE THE FILE...

            // Ensure the directory exists, if not, create it!
            if(!is_dir($this->image_directory)) mkdir($this->image_directory, null, true);

            // Move the file, overwriting if necessary
            Input::file('image')->move($this->image_directory, $filename);

            // Fix permissions on Windows (works on 2003?). This is because by default the uploaded imaged
            // does not inherit permissions from the folder it is moved to. Instead, it retains the
            // permissions of the temporary folder.
            exec('c:\windows\system32\icacls.exe ' . $this->image_paths[$filename] . ' /inheritance:e');

            return Redirect::to('registration/settings')->with('message', 'Image uploaded successfully!');
        }

    }

    private static function contains(&$haystack, $needle)
    {
        $result = strpos($haystack, $needle);
        return $result !== false;
    }

    //This is a mirror of the countries dropdown baked into cs-registration. Should be moved to a setting in the future.
    private $countries =  array(
                                        '' => '','Afghanistan' => 'Afghanestan',
                                        'Albania' => 'Shqiperia',
                                        'Algeria' => 'Al Jaza\'ir',
                                        'Andorra' => 'Andorra',
                                        'Angola' => 'Angola',
                                        'Antigua and Barbuda' => 'Antigua and Barbuda',
                                        'Argentina' => 'Argentina',
                                        'Armenia' => 'Hayastan',
                                        'Australia' => 'Australia',
                                        'Austria' => 'Oesterreich',
                                        'Azerbaijan' => 'Azarbaycan Respublikasi',
                                        'The Bahamas' => 'The Bahamas',
                                        'Bahrain' => 'Al Bahrayn',
                                        'Bangladesh' => 'Bangladesh',
                                        'Barbados' => 'Barbados',
                                        'Belarus' => 'Byelarus',
                                        'Belgium' => 'Belgie',
                                        'Belize' => 'Belice',
                                        'Benin' => 'Benin',
                                        'Bhutan' => 'Drukyul',
                                        'Bolivia' => 'Bolivia',
                                        'Bosnia and Herzegovina' => 'Bosna i Hercegovina',
                                        'Botswana' => 'Botswana',
                                        'Brazil' => 'Brasil',
                                        'Brunei' => 'Brunei',
                                        'Bulgaria' => 'Republika Bulgariya',
                                        'Burkina Faso' => 'Burkina Faso',
                                        'Burundi' => 'Burundi',
                                        'Cambodia' => 'Kampuchea',
                                        'Cameroon' => 'Cameroon',
                                        'Canada' => 'Canada',
                                        'Cape Verde' => 'Cabo Verde',
                                        'Central African Republic' => 'Republique Centrafricaine',
                                        'Chad' => 'Tchad',
                                        'Chile' => 'Chile',
                                        'China' => 'China',
                                        'Colombia' => 'Colombia',
                                        'Comoros' => 'Comores',
                                        'Congo, Republic of the' => 'Republique du Congo',
                                        'Congo, Democratic Republic of the' => 'Republique Democratique du Congo',
                                        'Costa Rica' => 'Costa Rica',
                                        'Cote d\'Ivoire' => 'Cote d\'Ivoire',
                                        'Croatia' => 'Hrvatska',
                                        'Cuba' => 'Cuba',
                                        'Cyprus' => 'Kypros',
                                        'Czech Republic' => 'Ceska Republika',
                                        'Denmark' => 'Danmark',
                                        'Djibouti' => 'Djibouti',
                                        'Dominica' => 'Dominica',
                                        'Dominican Republic' => 'Republica Dominicana',
                                        'Ecuador' => 'Ecuador',
                                        'Egypt' => 'Misr',
                                        'El Salvador' => 'El Salvador',
                                        'Equatorial Guinea' => 'Guinea Ecuatorial',
                                        'Eritrea' => 'Ertra',
                                        'Estonia' => 'Eesti',
                                        'Ethiopia' => 'YeItyop\'iya',
                                        'Fiji' => 'Fiji',
                                        'Finland' => 'Suomi',
                                        'France' => 'France or Republique Francaise',
                                        'Gabon' => 'Gabon',
                                        'The Gambia' => 'The Gambia',
                                        'Georgia' => 'Sak\'art\'velo',
                                        'Germany' => 'Deutschland',
                                        'Ghana' => 'Ghana',
                                        'Greece' => 'Ellas',
                                        'Grenada' => 'Grenada',
                                        'Guatemala' => 'Guatemala',
                                        'Guinea' => 'Guinee',
                                        'Guinea-Bissau' => 'Guine-Bissau',
                                        'Guyana' => 'Guyana',
                                        'Haiti' => 'Haiti',
                                        'Honduras' => 'Honduras',
                                        'Hungary' => 'Magyarorszag',
                                        'Iceland' => 'Island',
                                        'India' => 'India, Bharat',
                                        'Indonesia' => 'Indonesia',
                                        'Iran' => 'Iran, Persia',
                                        'Iraq' => 'Al Iraq',
                                        'Ireland' => 'Ireland or Eire',
                                        'Israel' => 'Yisra\'el',
                                        'Italy' => 'Italia',
                                        'Jamaica' => 'Jamaica',
                                        'Japan' => 'Nippon',
                                        'Jordan' => 'Al Urdun',
                                        'Kazakhstan' => 'Qazaqstan',
                                        'Kenya' => 'Kenya',
                                        'Kiribati' => 'Kiribati',
                                        'Korea, North' => 'Choson or Choson-minjujuui-inmin-konghwaguk',
                                        'Korea, South' => 'Taehan-min\'guk',
                                        'Kuwait' => 'Al Kuwayt',
                                        'Kyrgyzstan' => 'Kyrgyz Respublikasy',
                                        'Laos' => 'Sathalanalat Paxathipatai Paxaxon Lao',
                                        'Latvia' => 'Latvija',
                                        'Lebanon' => 'Lubnan',
                                        'Lesotho' => 'Lesotho',
                                        'Liberia' => 'Liberia',
                                        'Libya' => 'Libya',
                                        'Liechtenstein' => 'Liechtenstein',
                                        'Lithuania' => 'Lietuva',
                                        'Luxembourg' => 'Luxembourg',
                                        'Macedonia' => 'Makedonija',
                                        'Madagascar' => 'Madagascar',
                                        'Malawi' => 'Malawi',
                                        'Malaysia' => 'Malaysia',
                                        'Maldives' => 'Dhivehi Raajje',
                                        'Mali' => 'Mali',
                                        'Malta' => 'Malta',
                                        'Marshall Islands' => 'Marshall Islands',
                                        'Mauritania' => 'Muritaniyah',
                                        'Mauritius' => 'Mauritius',
                                        'Mexico' => 'Mexico',
                                        'Federated States of Micronesia' => 'Federated States of Micronesia',
                                        'Moldova' => 'Moldova',
                                        'Monaco' => 'Monaco',
                                        'Mongolia' => 'Mongol Uls',
                                        'Morocco' => 'Al Maghrib',
                                        'Mozambique' => 'Mocambique',
                                        'Myanmar (Burma)' => 'Myanma Naingngandaw',
                                        'Namibia' => 'Namibia',
                                        'Nauru' => 'Nauru',
                                        'Nepal' => 'Nepal',
                                        'Netherlands' => 'Nederland',
                                        'New Zealand' => 'New Zealand',
                                        'Nicaragua' => 'Nicaragua',
                                        'Niger' => 'Niger',
                                        'Nigeria' => 'Nigeria',
                                        'Norway' => 'Norge',
                                        'Oman' => 'Uman',
                                        'Pakistan' => 'Pakistan',
                                        'Palau' => 'Belau',
                                        'Panama' => 'Panama',
                                        'Papua New Guinea' => 'Papua New Guinea',
                                        'Paraguay' => 'Paraguay',
                                        'Peru' => 'Peru',
                                        'Philippines' => 'Pilipinas',
                                        'Poland' => 'Polska',
                                        'Portugal' => 'Portugal',
                                        'Qatar' => 'Qatar',
                                        'Romania' => 'Romania',
                                        'Russia' => 'Rossiya',
                                        'Rwanda' => 'Rwanda',
                                        'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
                                        'Saint Lucia' => 'Saint Lucia',
                                        'Samoa' => 'Samoa',
                                        'San Marino' => 'San Marino',
                                        'Sao Tome and Principe' => 'Sao Tome e Principe',
                                        'Saudi Arabia' => 'Al Arabiyah as Suudiyah',
                                        'Senegal' => 'Senegal',
                                        'Serbia and Montenegro' => 'Srbija-Crna Gora',
                                        'Seychelles' => 'Seychelles',
                                        'Sierra Leone' => 'Sierra Leone',
                                        'Singapore' => 'Singapore',
                                        'Slovakia' => 'Slovensko',
                                        'Slovenia' => 'Slovenija',
                                        'Solomon Islands' => 'Solomon Islands',
                                        'Somalia' => 'Somalia',
                                        'South Africa' => 'South Africa',
                                        'Spain' => 'Espana',
                                        'Sri Lanka' => 'Sri Lanka',
                                        'Sudan' => 'As-Sudan',
                                        'Suriname' => 'Suriname',
                                        'Swaziland' => 'Swaziland',
                                        'Sweden' => 'Sverige',
                                        'Switzerland' => 'Suisse (French)',
                                        'Syria' => 'Suriyah',
                                        'Taiwan' => 'T\'ai-wan',
                                        'Tajikistan' => 'Jumhurii Tojikistan',
                                        'Tanzania' => 'Tanzania',
                                        'Thailand' => 'Muang Thai',
                                        'Tolo' => 'Togo',
                                        'Tonga' => 'Tonga',
                                        'Trinidad and Tobago' => 'Trinidad and Tobago',
                                        'Tunisia' => 'Tunis',
                                        'Turkey' => 'Turkiye',
                                        'Turkmenistan' => 'Turkmenistan',
                                        'Tuvalu' => 'Tuvalu',
                                        'Uganda' => 'Uganda',
                                        'Ukraine' => 'Ukrayina',
                                        'United Arab Emirates' => 'Al Imarat al Arabiyah al Muttahidah',
                                        'United Kingdom' => 'United Kingdom',
                                        'United States' => 'United States',
                                        'Uruguay' => 'Uruguay',
                                        'Uzbekistan' => 'Uzbekiston Respublikasi',
                                        'Vanuatu' => 'Vanuatu',
                                        'Vatican City (Holy See)' => 'Santa Sede (Citta del Vaticano)',
                                        'Venezuela' => 'Venezuela',
                                        'Vietnam' => 'Viet Nam',
                                        'Yemen' => 'Al Yaman',
                                        'Zambia' => 'Zambia',
                                        'Zimbabwe' => 'Zimbabwe');
}
