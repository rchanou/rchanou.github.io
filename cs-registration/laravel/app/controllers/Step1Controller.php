<?php

require_once(app_path().'/tools/CS_API.php');
require_once(app_path().'/config/resources/strings.php');
require_once(app_path().'/config/resources/images.php');
require_once(app_path().'/config/resources/settings.php');

class Step1Controller extends BaseController
{
    //###################################
    //# STEP 1 - REGISTRATION HOME PAGE #
    //###################################

    /*
     * Step 1 presents the user with two registration options: Regular and Facebook.
     * It is also critically involved in starting the session, and fetching data from Club Speed.
     *
     * Step 1 flow:
     *
     * On entering this page:
     * - All sessions are reset. Facebook is logged off. (This is important since many Kiosk apps on the iPad
     * will automatically go back to the home page after a pre-determined idle time.)
     * - Strings, images, and settings are fetched from Club Speed. May be left as defaults.
     * - Strings, images, and settings are inserted into the session and the view.
     * - The language is set to either English or the CurrentCulture acquired from Club Speed.
     * - The step 1 view is created.
     */
    public function step1()
    {
        //Start the session from scratch if our visiting the page isn't due to a language change
        if (!Session::has("currentCultureChanged"))
        {
            Session::flush();
            Session::regenerate();
        }

        //Determine the strings to use for the website - contact Club Speed
        $strings = $this->determineStringsAndCulture();

        if ($strings == false) //If Club Speed couldn't be reached, go to the Disconnected screen
        {
            return Redirect::to('/disconnected');
        }
        if (Session::has("currentCultureChanged")) //Change languages if requested to do so
        {
            $newCulture = Session::get("currentCultureChanged");
            $translations = Session::get("translations");
            if (array_key_exists($newCulture, $translations))
            {
                $strings = $translations[$newCulture];
                Session::put("currentCulture",$newCulture);
                Session::put("currentCultureFB", CS_API::convertCultureToFacebook($newCulture));
            }
        }

        $strings["cultureNames"] = Strings::getCultureNames(); //Get the list of languages that the site can support
        Session::put('strings',$strings);

        //Determine the images to use for the website - contact Club Speed and use defaults if necessary
        $images = $this->determineImages();
        Session::put('images',$images);

        //Determine the settings to use for the website - contact Club Speed and use defaults if necessary
        $settings = $this->determineSettings();
        Session::put('settings',$settings);
        $strings = Session::get('strings');

        Session::put('initialized',true); //Set the site's state to initialized so other steps can be visited

        //Ensure that we can still connect to the Club Speed API. If not, direct to an error page.
        if (CS_API::cannotConnectToClubSpeedAPI())
        {
            return Redirect::to('/disconnected');
        }

        $session = Session::all();

        return View::make('/steps/step1', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB']));
    }

    //####################
    //# HELPER FUNCTIONS #
    //####################

    /**
     * This function pulls translation strings and culture information from Club Speed.
     * If encountering any issues with pulling this data, this function returns the default English strings.
     * Otherwise, it returns the strings in the specified current culture's language.
     * If any strings are missing from the specified current culture, they are replaced by the default English strings.
     * The default English strings can be overwritten simply by setting the current culture to English and providing
     * custom English strings via Club Speed.
     *
     * @return mixed An array of strings to use throughout all of the website's templates.
     */
    private function determineStringsAndCulture()
    {

        $stringTranslations = CS_API::call("getTranslations");
        $currentCulture = CS_API::call("getCurrentCulture");

        if ($stringTranslations === false || $currentCulture === false) //If Club Speed could not be reached
        {
            //Default to English strings
            $stringTranslations = array();
            $currentCulture = "en-US";

            Session::put('currentCulture',$currentCulture);
            Session::put('currentCultureFB',CS_API::convertCultureToFacebook($currentCulture));
            Session::put('supportedCultures', array('en-US'));

            $stringTranslations["en-US"] = Strings::getDefaultEnglish();
            Session::put('translations', $stringTranslations);
            return $stringTranslations[$currentCulture];
        }
        else //If we were able to contact Club Speed and get strings
        {
            $this->checkClubSpeedStrings($stringTranslations); //Check the Club Speed strings to ensure none are missing

            //Determine all supported cultures and replace any missing fields with English fields
            $supportedCultures = array();
            foreach($stringTranslations as $cultureName => $cultureTranslations)
            {
                $supportedCultures[$cultureName] = $cultureName;
                $stringTranslations[$cultureName] = array_merge(Strings::getDefaultEnglish(),$stringTranslations[$cultureName]);
            }

            //If English isn't supported, insert our default English strings
            if (!array_key_exists("en-US",$supportedCultures))
            {
                $supportedCultures["en-US"] = "en-US";
                array_push($stringTranslations,array("en-US" => Strings::getDefaultEnglish()));
            }

            Session::put('supportedCultures',$supportedCultures); //Store all supported cultures for future use
            Session::put('translations',$stringTranslations); //Store all translations for future use

            if (array_key_exists($currentCulture,$stringTranslations)) //If the specified current culture has translations
            {
                Session::put('currentCulture',$currentCulture);
                Session::put('currentCultureFB',CS_API::convertCultureToFacebook($currentCulture));
                return $stringTranslations[$currentCulture]; //Those are the strings we want to use for now
            }
            else //If the translations are missing, default to English
            {
                Session::put('currentCulture',"en-US");
                Session::put('currentCultureFB',"en_US");

                return Strings::getDefaultEnglish();
            }
        }
    }

    /**
     * This function asks Club Speed for any custom images. If any are present, they overwrite the default images.
     * Otherwise, the default images apply.
     * @return mixed An array of images to be used through the entire website's template.
     */
    private function determineImages()
    {
        return Images::getDefaultImages(); //This is temporary. Images will either be pulled from an API or from /assets

        $images = CS_API::call("getImages");
        if ($images === false) //If we couldn't pull any images from Club Speed
        {
            return Images::getDefaultImages(); //Just use the default
        }
        else
        {
            $images = array_merge(Images::getDefaultImages(),$images); //Overwrite the default images with the new images
        }
        return $images;
    }

    /**
     * This function asks Club Speed for any custom settings. If any are present, they overwrite the default settings.
     * Otherwise, the default settings apply.
     * @return mixed An array of settings to be used through the entire website's template.
     */
    private function determineSettings()
    {
        $settings = CS_API::call("getSettings");

        if ($settings === false) //If we couldn't pull any settings from Club Speed
        {
            $settings = Settings::getDefaultSettings(); //Just use the default
        }
        else
        {
            $settings = array_merge(Settings::getDefaultSettings(),$settings); //Overwrite the default settings with the new settings
            $settings['dropdownOptions'] = array();

            $strings = Session::get('strings');

            $settings['dropdownOptions']['0'] = $strings['str_defaultSourceText'];
            foreach($settings["Sources"] as $currentSource)
            {
                $settings['dropdownOptions'][$currentSource["SourceID"]] = $currentSource["SourceName"];
            }

            $settings['eventGroupIDOptions'] = array();

            if (array_key_exists('eventGroups',$settings))
            {
                foreach($settings["eventGroups"] as $currentEventGroup)
                {
                    foreach($currentEventGroup as $eventID => $eventName)
                    {
                        $settings['eventGroupIDOptions'][$eventID] = $eventName;
                    }
                }
            }
        }

        //Assuming that the user is not a minor to start with
        $settings['isMinor'] = false;

        //Always required fields
        $settings['showBirthDate'] = true;
        $settings['requireBirthDate'] = true;
        $settings['showFirstName'] = true;
        $settings['showLastName'] = true;

        if (!array_key_exists('CfgRegDisblEmlForMinr',$settings)) //default if setting is missing
        {
            $settings['CfgRegDisblEmlForMinr'] = false;
        }

        if (!array_key_exists('AgeAllowOnlineReg',$settings)) //default if setting is missing
        {
            $settings['AgeAllowOnlineReg'] = 18;
        }

        //Determine if we're using an IP Camera, and if we don't have the settings already, put its IP address in the session
        if (Input::has('terminal') && !Session::has("ipCamURL"))
        {
            $ipcam = Input::get('terminal');
            $ipCamURL = CS_API::call("getCameraIP",array("terminalName" => $ipcam));
            if ($ipCamURL != null && $ipCamURL != false)
            {
                $ipCamURL = strtolower($ipCamURL);
                Session::put('ipCamURL',$ipCamURL);
                Session::put('ipcam',$ipcam);
            }

        }

        $strings = Session::get('strings');

        if (array_key_exists('CustomText1',$settings)) { $strings['str_Custom1'] = $settings['CustomText1']; }
        if (array_key_exists('CustomText2',$settings)) { $strings['str_Custom2'] = $settings['CustomText2']; }
        if (array_key_exists('CustomText3',$settings)) { $strings['str_Custom3'] = $settings['CustomText3']; }
        if (array_key_exists('CustomText4',$settings)) { $strings['str_Custom4'] = $settings['CustomText4']; }

        Session::put('strings',$strings);

        if (array_key_exists('Reg_CaptureProfilePic',$settings))
        {
            $settings['Reg_CaptureProfilePic'] = filter_var(strtolower($settings['Reg_CaptureProfilePic']),FILTER_VALIDATE_BOOLEAN);
        }
        else
        {
            $settings['Reg_CaptureProfilePic'] = false;
        }

        //TESTING - Forces every field to be visible
        /*$settings['Reg_CaptureProfilePic'] = true; //Replaced showPicture config setting
        $settings['CfgRegAddShow'] = true;
        $settings['CfgRegCntryShow'] = true;
        $settings['CfgRegCityShow'] = true;
        $settings['CfgRegStateShow'] = true;
        $settings['CfgRegZipShow'] = true;
        $settings['CfgRegRcrNameShow'] = true;
        $settings['showBirthDate'] = true;
        $settings['CfgRegPhoneShow'] = true;
        $settings['CfgRegSrcShow'] = true;
        $settings['CfgRegEmailShow'] = true;
        $settings['CfgRegEmailReq'] = true;

        $settings['cfgRegCustTxt1Show'] = true;
        $settings['cfgRegCustTxt1req'] = true;
        $settings['cfgRegCustTxt2Show'] = true;
        $settings['cfgRegCustTxt3Show'] = true;
        $settings['cfgRegCustTxt4Show'] = true;
        $settings['CfgRegValidateGrp'] = true;
        $settings['CfgRegDrvrLicShow'] = true;
        $settings['CfgRegDrvrLicReq'] = true;
        $settings['Reg_CaptureProfilePic'] = true;  //This is replacing showPicture in config.php
        $settings['CfgRegUseMsign'] = true;*/
        //END TESTING

        return $settings;
    }

    public function checkClubSpeedStrings($stringsFromClubSpeed)
    {
        if (array_key_exists('en-US',$stringsFromClubSpeed)) //If there are English strings, grab them
        {
            $stringsFromClubSpeed = $stringsFromClubSpeed['en-US'];
        }
        else
        {
            $stringsFromClubSpeed = array();
        }

        $appStrings = Strings::getDefaultEnglish(); //Grab the app's expected strings

        $stringsClubSpeedIsMissing = array_diff_key($appStrings,$stringsFromClubSpeed); //See if Club Speed is missing any
        if (count($stringsClubSpeedIsMissing) > 0) //If any strings are missing
        {
            //Format the missing string data as expected by Club Speed's API
            $stringsClubSpeedIsMissingFormatted = array('batch' => array());
            foreach($stringsClubSpeedIsMissing as $stringLabel => $stringValue)
            {
                $stringsClubSpeedIsMissingFormatted['batch'][] = array('name' => $stringLabel,
                                                              'namespace' => 'Translations.Registration',
                                                              'value' => $stringValue,
                                                              'language' => null,
                                                              'comment' => '');
            }
            //var_dump($stringsClubSpeedIsMissingFormatted);
            //die();

            //Send Club Speed the missing strings
            $result = CS_API::call("sendMissingStrings",array($stringsClubSpeedIsMissingFormatted));
            //var_dump($result);
            //die();
        }
    }
} 