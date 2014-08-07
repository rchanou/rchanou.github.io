<?php
include(app_path().'/tools/signature-to-image/signature-to-image.php');
include(app_path().'/tools/CS_API.php');
include(app_path().'/config/resources/strings.php');
include(app_path().'/config/resources/images.php');
include(app_path().'/config/resources/settings.php');

/**
 * Class RegistrationController
 *
 * This class controls the entirety of the iPad Registration Kiosk.
 */
class RegistrationController extends BaseController {

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

        if (!Session::has("currentCultureChanged"))
        {
            //Start the session from scratch if our visiting the page isn't due to a language change
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
                Session::put("currentCultureFB", $this->convertCultureToFacebook($newCulture));
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

        Session::put('initialized',true); //Set the site's state to initialized so other steps can be visited

        //Ensure that we can still connect to the Club Speed API. If not, direct to an error page.
        if ($this->cannotConnectToClubSpeedAPI())
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

    //####################################
    //# FAILURE TO CONNECT TO CLUB SPEED #
    //####################################

    /**
     * This page is displayed if there is any difficulty contacting Club Speed.
     * An error message is shown to the user, and they are encouraged to try again or to
     * contact Club Speed support.
     *
     * @return mixed This creates the actual view: disconnected.blade.php
     */
    public function disconnected()
    {
        //Define defaults and create the view
        $session['strings'] = Strings::getDefaultEnglish();
        $session['strings']["cultureNames"] = Strings::getCultureNames();
        $session['images'] = Images::getDefaultImages();
        $session['settings'] = Settings::getDefaultSettings();
        $session['currentCulture'] = "en-US";
        $session['currentCultureFB'] = "en_US";
        $session['translations'] = array("en-US" => $session['strings']);

        return View::make('/steps/disconnected', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB']));
    }

    //##############################
    //# STEP 2 - REGISTRATION FORM #
    //##############################

    /**
     * Step 2 allows the user to input their registration information, like Racer Name and their picture.
     *
     * Step 2 flow:
     *
     * - Session validity and language changes are checked.
     * - Whether or not the user is logged in to Facebook is determined.
     * - If they are, some fields are auto-populated.
     * - The view is created.
     *
     * @return mixed
     */
    public function step2()
    {

        $this->checkForLanguageChange();
        if($this->sessionIsInvalid())
        {
            return Redirect::to('/step1');
        }

        $session = Session::all();

        $oldInput = null;
        if (Session::has("oldInput")) //Restore previous input if present
        {
            $oldInput = Session::get("oldInput");
            Session::forget("oldInput");

            return Redirect::to('/step2')->withInput($oldInput);
        }

        /*print_r(json_encode($session['settings']));
        die();*/

        $numberOfItemsInAddressRow = 0;
        if ($session['settings']['CfgRegCityShow'])
            { $numberOfItemsInAddressRow++; }
        if ($session['settings']['CfgRegStateShow'])
            { $numberOfItemsInAddressRow++; }
        if ($session['settings']['CfgRegZipShow'])
            { $numberOfItemsInAddressRow++; }

        $columnClass = "col-sm-4";
        if ($numberOfItemsInAddressRow == 2)
        {
            $columnClass = "col-sm-6";
        }
        else if ($numberOfItemsInAddressRow == 1)
        {
            $columnClass = "col-sm-12";
        }

        return View::make('/steps/step2', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
        'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB'],
            'addressColumnClass' => $columnClass));
    }

    /**
     * Step 2 - POST
     *
     * This function is called when the user presses the Submit button after entering all
     * required registration information.
     *
     * - Validation is checked to ensure all expected data was received, and that it was formatted correctly.
     * - In the case of any errors, the user is redirected to Step 2 with the appropriate errors.
     * - If there was any camera input, the image is converted to base64 and stored as part of the session.
     * - The rest of the form input is stored in the session, and the user is directed to the next step.
     *
     * @return mixed Redirection to step3
     */
    public function postStep2()
    {
        $session = Session::all();
        $settings = $session['settings'];
        $input = Input::all();
        if (!array_key_exists('isMinor',$input))
        {
            $input['isMinor'] = false;
        }
        Session::put('isMinor',$input['isMinor']);
        $session['isMinor'] = $input['isMinor'];

        //Rules for validation - many are determined by the track itself via Club Speed
        $rules = array();
        if ($settings['showBirthDate'] && $settings['requireBirthDate'])
        {
            $rules['birthdate'] = 'required|before:today|date';
        }
        if ($settings['CfgRegPhoneShow'] && $settings['CfgRegPhoneReq'])
        {
            $rules['mobilephone'] = 'required';
        }
        if ($settings['CfgRegSrcShow'] && $settings['CfgRegSrcReq'])
        {
            $rules['howdidyouhearaboutus'] = 'required';
        }
        if ($settings['showFirstName'] && $settings['requireFirstName'])
        {
            $rules['firstname'] = 'required';
        }
        if ($settings['showLastName'] && $settings['requireLastName'])
        {
            $rules['lastname'] = 'required';
        }
        if ($settings['CfgRegRcrNameShow'] && $settings['CfgRegRcrNameReq'])
        {
            $rules['racername'] = 'required';
        }
        if ($settings['CfgRegEmailShow'] && !($input['isMinor'] && $settings['CfgRegDisblEmlForMinr']))
        {
            if ($settings['CfgRegEmailReq'])
            {
                $rules['email'] = 'required|email';
            }
            else
            {
                $rules['email'] = 'email';
            }

            if ($settings['AllowDuplicateEmail'] || Input::get("email") == "")
            {
                $isEmailTaken = false;
            }
            else
            {
                $searchByEmail = CS_API::call("searchByEmail", array("email" => Input::get("email")));
                if ($searchByEmail === false) //If the call failed
                {
                    return Redirect::to('/disconnected'); //Redirect to an error page
                }
                $isEmailTaken = count($searchByEmail["racers"]) > 0;
            }

            if ($isEmailTaken)
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', "This e-mail address has already been registered."); //TODO: Localize this string, switch to modal errors
                return Redirect::to('/step2')->withErrors($messages)->withInput();
            }

            if ($settings['CfgRegAddShow'] && $settings['CfgRegAddReq'])
            {
                $rules['Address'] = 'required';
            }
            if ($settings['CfgRegCntryShow'] && $settings['CfgRegCntryReq'])
            {
                $rules['Country'] = 'required';
            }
            if ($settings['CfgRegCityShow'] && $settings['CfgRegCityReq'])
            {
                $rules['City'] = 'required';
            }
            if ($settings['CfgRegStateShow'] && $settings['CfgRegStateReq'])
            {
                $rules['State'] = 'required';
            }
            if ($settings['CfgRegZipShow'] && $settings['CfgRegZipReq'])
            {
                $rules['Zip'] = 'required';
            }
        }

        //TODO: Localize these error messages
        //Error messages in case of validation failure
        $messages = array(
            'birthdate.required' => 'The birth date is required.',
            'birthdate.before' => 'The birth date must be in the past.',
            'birthdate.date' => 'The birth date must be a valid date.',
            'mobilephone.required' => 'Mobile phone is required.',
            'howdidyouhearaboutus.required' => 'How Did You Hear About Us is required.',
            'firstname.required' => 'First name is required.',
            'lastname.required' => 'Last name is required.',
            'racername.required' => 'Racer name is required.',
            'email.required' => 'E-mail address is required.',
            'email.email' => 'E-mail address must be valid.',
            'Address.required' => 'Address is required.',
            'Country.required' => 'Country is required.',
            'City.required' => 'City is required.',
            'State.required' => 'State is required.',
            'Zip.required' => 'Zip is required.'
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        // If validation fails, redirect
        if ($validator->fails()) {
            return Redirect::to('/step2')->withErrors($validator)->withInput();
        }

        //If there was an image selected, convert it and insert into session
        if (array_key_exists("cameraInput",$input))
        {
            if ($input["cameraInput"] != null)
            {
                $fileName = $_FILES["cameraInput"]["name"];

                $fileExtension = strtolower(substr($fileName,strpos($fileName,".") + 1));
                if ($fileExtension == "jpg")
                {
                    $fileExtension = "jpeg";
                }
                $input["cameraInput"] = $this->convertPathToImage($_FILES["cameraInput"]["tmp_name"],$fileExtension);
            }
        }
        else
        {
            $input["cameraInput"] = null;
        }
        $input["consenttoemail"] = Input::has("consenttoemail") ? true : false;

        Session::put('formInput',$input); //Insert all form input into session

        Session::put('oldInput',$input);

        return Redirect::to('/step3');

    }

    //##########################################
    //# STEP 3 - TERMS AND CONDITION SIGNATURE #
    //##########################################

    /**
     * Step 3 presents the Terms & Conditions to the user, and allows for acceptance (with signature) or declination.
     *
     * Step 3 flow:
     * - Session validity and language changes are checked.
     * - The view is created.
     *
     * @return mixed Step 3 view is created
     */
    public function step3()
    {
        $this->checkForLanguageChange();
        if($this->sessionIsInvalid() || !Session::has("formInput"))
        {
            return Redirect::to('/step1');
        }

        $session = Session::all();

        return View::make('/steps/step3', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB'],
            'formInput' => $session['formInput'])
            );
    }

    /**
     * This function is called after the user signs their name and clicks "I agree, sign".
     * Their signature is converted to a base64 encoded PNG and added to the session.
     * Their information is then sent to Club Speed for registration.
     * They are then redirected to the final step, depending on the results.
     *
     * @return mixed
     */
    public function postStep3()
    {
        $signatureJSON = Input::get("signatureOutput");
        if ($signatureJSON != null)
        {
            $base64 = $this->convertSignatureToBase64PNG($signatureJSON);
        }
        else
        {
            $base64 = null;
        }
        $formInput = Session::get("formInput");
        $formInput["signature"] = $base64;

        $session = Session::all();
        $settings = $session['settings'];

        if ($settings['showPicture'] && $formInput["facebookProfileURL"] != "#")
        {
            $base64 = $this->convertPathToImage($formInput["facebookProfileURL"]);
            $formInput["cameraInput"] = $base64;
        }

        Session::put("formInput", $formInput);
        Session::put("signatureAcquired", true);

        $settings = Session::get("settings");
        $clubSpeedCustomerData = array(
              "birthdate" => $formInput["birthdate"],
              "mobilephone" => $formInput["mobilephone"],
              "howdidyouhearaboutus" => $formInput["howdidyouhearaboutus"],
              "firstname" => $formInput["firstname"],
              "lastname" => $formInput["lastname"],
              "racername" => $formInput["racername"],
              "email" => isset($formInput["email"]) ? $formInput["email"] : "",
              "donotemail" => (!$formInput["consenttoemail"]),
              "profilephoto" => $formInput["cameraInput"],
              "signaturephoto" => $formInput["signature"],
              "gender" => $formInput["gender"],
              "BusinessName" => $settings["BusinessName"],
              "Waiver1" => $settings["Waiver1"],
              "Waiver2" => $settings["Waiver2"],
              "isMinor" => Session::get("isMinor"),
              "Address" => isset($formInput["Address"]) ? $formInput["Address"] : "",
              "Address2" => isset($formInput["Address2"]) ? $formInput["Address2"] : "",
              "Country" => isset($formInput["Country"]) ? $formInput["Country"] : "",
              "City" => isset($formInput["City"]) ? $formInput["City"] : "",
              "State" => isset($formInput["State"]) ? $formInput["State"] : "",
              "Zip" => isset($formInput["Zip"]) ? $formInput["Zip"] : ""
        );

      //Useful debugging output
/*        echo '<h1>Data sent to API</h1>';
        echo '<b>Birth date: </b>' .  $clubSpeedCustomerData["birthdate"] . '<br/>';
        echo '<b>Mobile phone: </b>' .  $clubSpeedCustomerData["mobilephone"] . '<br/>';
        echo '<b>How did you hear about us?: </b>' .  $clubSpeedCustomerData["howdidyouhearaboutus"] . '<br/>';
        echo '<b>First name: </b>' .  $clubSpeedCustomerData["firstname"] . '<br/>';
        echo '<b>Last name: </b>' .  $clubSpeedCustomerData["lastname"] . '<br/>';
        echo '<b>Racer name: </b>' .  $clubSpeedCustomerData["racername"] . '<br/>';
        echo '<b>Email: </b>' .  $clubSpeedCustomerData["email"] . '<br/>';
        echo '<b>Do not e-mail: </b>';
        echo $clubSpeedCustomerData["donotemail"] ? "true" : "false";
        echo '<br/>';
        echo '<b>Gender: </b>' .  $clubSpeedCustomerData["gender"] . '<br/>';
        echo '<b>Business Name: </b>' . Session::get("settings")["BusinessName"] . '<br/>';
        echo '<b>Waiver1</b> ' . Session::get("settings")["Waiver1"] . '<br/>';
        //echo '<b>Facebook profile URL: </b>' . $formInput["facebookProfileURL"] . '<br/>';
        echo '<b>Profile photo:</b> <br/><img src="'.  $clubSpeedCustomerData["profilephoto"]  . '"><br/>';
        echo '<b>Signature photo:</b> <br/><img src="'.  $clubSpeedCustomerData["signaturephoto"]  . '"><br/>';
        echo '<b>Address: </b>' . $clubSpeedCustomerData["Address"] . '<br/>';
        echo '<b>Address2: </b>' . $clubSpeedCustomerData["Address2"] . '<br/>';
        echo '<b>Country: </b>' . $clubSpeedCustomerData["Country"] . '<br/>';
        echo '<b>City: </b>' . $clubSpeedCustomerData["City"] . '<br/>';
        echo '<b>State: </b>' . $clubSpeedCustomerData["State"] . '<br/>';
        echo '<b>Zip: </b>' . $clubSpeedCustomerData["Zip"] . '<br/>';
        die();*/

        $result = CS_API::call("registerCustomer",$clubSpeedCustomerData);

        if ($result === false) //If the call failed
        {
            return Redirect::to('/disconnected'); //Redirect to an error page
        }

        return Redirect::to('/step4');
    }

    //##################################
    //# STEP 4 - REGISTRATION COMPLETE #
    //##################################

    /**
     * Step 4 displays a Registration Complete message, and offers a link back to the first page for future registrants.
     *
     * Step 4 flow:
     * - Session validity and language changes are checked.
     * - The view is created.
     * @return mixed
     */
    public function step4()
    {
        $this->checkForLanguageChange();
        if($this->sessionIsInvalid() || !Session::has("signatureAcquired"))
        {
            return Redirect::to('/step1');
        }

        Session::put("sessionComplete", true);

        $session = Session::all();
        return View::make('/steps/step4', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB']) );
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

            /*
             * International testing for Shakib
             */

            /*$stringTranslations["es-MX"] = array_merge(Strings::getDefaultEnglish(),Strings::getDefaultSpanish());
            $stringTranslations["en-GB"] = Strings::getDefaultEnglish();
            $stringTranslations["en-NZ"] = Strings::getDefaultEnglish();
            $stringTranslations["en-AU"] = Strings::getDefaultEnglish();
            $stringTranslations["en-IE"] = Strings::getDefaultEnglish();
            $stringTranslations["en-CA"] = Strings::getDefaultEnglish();
            $stringTranslations["es-ES"] = Strings::getDefaultEnglish();
            $stringTranslations["es-PR"] = Strings::getDefaultEnglish();
            $stringTranslations["ru-RU"] = Strings::getDefaultEnglish();
            $stringTranslations["fr-CA"] = Strings::getDefaultEnglish();
            $stringTranslations["de-DE"] = Strings::getDefaultEnglish();
            $stringTranslations["nl-NL"] = Strings::getDefaultEnglish();
            $stringTranslations["pl-PL"] = Strings::getDefaultEnglish();
            $stringTranslations["da-DK"] = Strings::getDefaultEnglish();
            $stringTranslations["ar-AE"] = Strings::getDefaultEnglish();
            $stringTranslations["it-IT"] = Strings::getDefaultEnglish();
            $stringTranslations["bg-BG"] = Strings::getDefaultEnglish();
            $stringTranslations["sv-SE"] = Strings::getDefaultEnglish();*/

            Session::put('currentCulture',$currentCulture);
            Session::put('currentCultureFB',$this->convertCultureToFacebook($currentCulture));
            Session::put('supportedCultures', array('en-US')); //TODO: Revert to this
            /*Session::put('supportedCultures', array('en-US','en-GB', 'en-NZ', 'en-AU', 'en-IE', 'en-CA', 'es-MX',
                'es-ES','es-PR','ru-RU','fr-CA','de-DE','nl-NL','pl-PL',
                'da-DK', 'ar-AE','it-IT','bg-BG','sv-SE'));*/

            $stringTranslations["en-US"] = Strings::getDefaultEnglish();
            Session::put('translations', $stringTranslations);
            return $stringTranslations[$currentCulture];


            //return false;

            /*
             *
            //For testing purposes:
            $stringTranslations = array();
            $currentCulture = "en-US";

            //Default to English strings
            Session::put('currentCulture',$currentCulture);
            Session::put('currentCultureFB',$this->convertCultureToFacebook($currentCulture));
            Session::put('supportedCultures', array('en-US'));

            $stringTranslations["en-US"] = Strings::getDefaultEnglish();
            Session::put('supportedCultures', array('en-US','en-GB', 'en-NZ', 'en-AU', 'en-IE', 'en-CA', 'es-MX',
            'es-ES','es-PR','ru-RU','fr-CA','de-DE','nl-NL','pl-PL',
            'da-DK', 'ar-AE','it-IT','bg-BG','sv-SE'));

            $supportedCultures["es-MX"] = "es-MX";*/

/*          $stringTranslations["es-MX"] = array_merge(Strings::getDefaultEnglish(),Strings::getDefaultSpanish());
            $stringTranslations["en-GB"] = Strings::getDefaultEnglish();
            $stringTranslations["en-NZ"] = Strings::getDefaultEnglish();
            $stringTranslations["en-AU"] = Strings::getDefaultEnglish();
            $stringTranslations["en-IE"] = Strings::getDefaultEnglish();
            $stringTranslations["en-CA"] = Strings::getDefaultEnglish();
            $stringTranslations["es-ES"] = Strings::getDefaultEnglish();
            $stringTranslations["es-PR"] = Strings::getDefaultEnglish();
            $stringTranslations["ru-RU"] = Strings::getDefaultEnglish();
            $stringTranslations["fr-CA"] = Strings::getDefaultEnglish();
            $stringTranslations["de-DE"] = Strings::getDefaultEnglish();
            $stringTranslations["nl-NL"] = Strings::getDefaultEnglish();
            $stringTranslations["pl-PL"] = Strings::getDefaultEnglish();
            $stringTranslations["da-DK"] = Strings::getDefaultEnglish();
            $stringTranslations["ar-AE"] = Strings::getDefaultEnglish();
            $stringTranslations["it-IT"] = Strings::getDefaultEnglish();
            $stringTranslations["bg-BG"] = Strings::getDefaultEnglish();
            $stringTranslations["sv-SE"] = Strings::getDefaultEnglish();

            Session::put('translations', $stringTranslations);

            return $stringTranslations[$currentCulture];*/


        }
        else //If we were able to contact Club Speed and get strings
        {
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
                Session::put('currentCultureFB',$this->convertCultureToFacebook($currentCulture));
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
        return Images::getDefaultImages(); //TODO: Temporary, for testing purposes, need to extend stub API to include default images

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

            $settings['dropdownOptions']['0'] = "";
            foreach($settings["Sources"] as $currentSource)
            {
                $settings['dropdownOptions'][$currentSource["SourceID"]] = $currentSource["SourceName"];
            }

/*            print_r(json_encode($settings['dropdownOptions']));
            die();*/
        }

        if (Config::has('config.showPicture'))
        {
            $showPicture = Config::get('config.showPicture');
            $settings['showPicture'] = $showPicture;
        }

        //Assuming that the user is not a minor to start with
        $settings['isMinor'] = false;

        //Forcing birth date to be required, as stated by Shakib
        $settings['showBirthDate'] = true;
        $settings['requireBirthDate'] = true;

        //Forcing Facebook to be enabled
        $settings["Reg_EnableFacebook"] = true;

        if (!array_key_exists('CfgRegDisblEmlForMinr',$settings))
        {
            $settings['CfgRegDisblEmlForMinr'] = false;
        }
        //TODO: Just for test purposes
/*        $settings['CfgRegDisblEmlForMinr'] = false;

        $settings['CfgRegEmailReq'] = true; //Forcing e-mail to be required, test purposes
        $settings['AllowDuplicateEmail'] = false; //Forcing e-mail duplicates to be allowed, test purposes
        $settings['cfgRegAllowMinorToSign'] = false; //Forcing minors to be allowed to sign, test purposes
        $settings['CfgRegDisblEmlForMinr'] = true; //Forcing, test purposes, etc*/

        /*$settings['cfgRegAllowMinorToSign'] = true;

        //TODO: Forcing racer name to be required for testing purposes.
        $settings["CfgRegRcrNameReq"] = true;
        $settings["CfgRegRcrNameShow"] = true;*/


        return $settings;
    }

    /**
     * Given a JSON object in Signature-Pad's format, this function converts it to a base64-encoded PNG and returns it to the user.
     * @param string $signatureJSON A Signature-Pad formatted JSON object representing a signature.
     * @return string A base64 encoded PNG.
     */
    private function convertSignatureToBase64PNG($signatureJSON)
    {
        //Convert the signature to an image resource
        $signatureImage = sigJsonToImage($signatureJSON, array(
            'imageSize' => array(850, 478) //array(160,90) array(850, 250)
        ,'bgColour' => array(0xff, 0xff, 0xff)
        ,'penWidth' => 2
        ,'penColour' => array(0x14, 0x53, 0x94)
        ,'drawMultiplier'=> 4
        ));

        //Resizing to Club Speed signature size
        $imgDest = imagecreatetruecolor(160, 90);
        imagecopyresampled($imgDest,$signatureImage,0,0,0,0,160,90,850,478);
        $signatureImage = $imgDest;

        //Convert the image resource to a png
        ob_start();
        imagepng($signatureImage);
        $stringdata = ob_get_contents();
        ob_end_clean();

        //Convert the png to base64
        $base64 = 'data:image/' . 'png' . ';base64,' . base64_encode($stringdata);

        //Experimental
        /*echo "<img style='border: 1px solid black;' src=\"$base64\">";
        die();*/

        return $base64;
    }

    /**
     * This function is called when an option in the language dropdown menu is selected.
     * This is achieved by causing the dropdown selection to redirect to /changeLanguage/newLanguageCode/destinationStep
     * It results in a redirect to the step that originated the dropdown change, and lets that step know to change languages.
     * @param string $newLanguageCode The language code to switch to. Ex. "en-US"
     * @param string $destinationStep The step to redirect to. This is the same step that the dropdown was selected from.
     * @return mixed Redirect to the originating step, adding in a culture change request to the session.
     */
    public function changeLanguage($newLanguageCode, $destinationStep)
    {
        return Redirect::to($destinationStep)->withInput()->with('currentCultureChanged',$newLanguageCode);
    }

    /**
     * This function is used to check if we need to redirect to the home page for any reason.
     * This would happen if future steps were navigated to before prior steps were completed, or if past steps
     * were to be visited after registration had completed.
     * @return bool True if a session was not initialized and we are at any step other than step1, or if a session is
     * complete and we are at any step other than the last. False otherwise.
     */
    private function sessionIsInvalid()
    {
        if (!Session::has('initialized') || Session::has('sessionComplete'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * This function is called to check for a potential language changed.
     * This is determined by checking for the existence of "currentCultureChanged" in the session.
     * If a change was requested, current strings and culture are switched if the desired new language exists in memory.
     */
    private function checkForLanguageChange()
    {
        if (Session::has("currentCultureChanged"))
        {
            $newCulture = Session::get("currentCultureChanged");
            $translations = Session::get("translations");
            if (array_key_exists($newCulture, $translations))
            {
                $strings = $translations[$newCulture];
                $strings["cultureNames"] = Strings::getCultureNames();
                Session::put('strings',$strings);
                Session::put("currentCulture",$newCulture);
                Session::put("currentCultureFB", $this->convertCultureToFacebook($newCulture));
            }
        }
    }

    /**
     * This function takes the standard localization format, "en-US", and converts it to Facebook's format, "en_US".
     * It also recognizes formats that Facebook does not directly support, and converts to the nearest one instead.
     * @param string $currentCulture The current culture in a standard en-US format.
     * @return string Facebook's format of that culture: en_US.
     */
    private function convertCultureToFacebook($currentCulture)
    {
        $currentCulture = strtolower(substr($currentCulture,0,2)) . '_' . strtoupper(substr($currentCulture,3,2));
        switch ($currentCulture)
        {
            case "es_MX":
            case "es_PR":
                return "es_LA";
                break;
            case "ar_AE":
                return "ar_AR";
                break;
        }
        return $currentCulture;
    }

    /**
     * This function, given the path to an image and optionally its type, converts it to base64.
     * @param string $path Path of the image
     * @param string $type Optional image type (to overwrite .tmp file extensions)
     * @return string A base64 encoded version of the image
     */
    private function convertPathToImage($path,$type = "")
    {
        if ($type == "")
        {
            $type = pathinfo($path, PATHINFO_EXTENSION);
        }
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    /**
     * This function pings the Club Speed API by trying to get the API version.
     * If any connectivity issue occurs, this returns true.
     * Otherwise, it returns false.
     * @return bool True if there's a connectivity problem, or false otherwise.
     */
    private function cannotConnectToClubSpeedAPI()
    {
       return !CS_API::call("checkAPI");
    }
} 