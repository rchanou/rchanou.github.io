<?php

require_once(app_path().'/tools/signature-to-image/signature-to-image.php');
require_once(app_path().'/tools/CS_API.php');
require_once(app_path().'/config/resources/strings.php');
require_once(app_path().'/config/resources/images.php');
require_once(app_path().'/config/resources/settings.php');

class Step3Controller extends BaseController {

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
        Session::forget('registrationStatus');
        CS_API::checkForLanguageChange();
        if(CS_API::sessionIsInvalid() || !Session::has("formInput"))
        {
            return Redirect::to(CS_API::getStep1URL());
        }

        $session = Session::all();

        $settings = $session['settings'];
        if ($settings['enableWaiverStep'])
        {
            return View::make('/steps/step3', array('strings' => $session['strings'],
                    'images' => $session['images'],
                    'settings' => $session['settings'],
                    'translations' => $session['translations'],
                    'currentCulture' => $session['currentCulture'],
                    'currentCultureFB' => $session['currentCultureFB'],
                    'formInput' => $session['formInput'],
                    'step1URL' => CS_API::getStep1URL())
            );
        }
        else //If the waiver step is disabled, just skip to registration
        {
            return $this->attemptCustomerRegistration();
        }

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
        $strings = Session::get('strings');
        if (Session::get('registrationStatus') == null)
        {
            Session::put('registrationStatus','processing');
            return $this->attemptCustomerRegistration();
        }
        else if (Session::get('registrationStatus') == 'processing')
        {
            sleep(2);
            return Redirect::action('Step3Controller@postStep3')->withInput();
        }
        else if (Session::get('registrationStatus') == 'failed')
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_problemWithRegistration']);
            Session::forget('registrationStatus');
            return Redirect::to('/step2')->withErrors($messages)->withInput();
        }
        else if (Session::get('registrationStatus') == 'complete')
        {
            return Redirect::to('/step4');
        }
    }

    private function attemptCustomerRegistration()
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

        if (!isset($formInput["cameraInput"]))
        {
            $formInput["cameraInput"] = "";
        }
        if ($formInput["cameraInput"] == null && $settings['Reg_CaptureProfilePic'] && array_key_exists("facebookProfileURL",$formInput) && $formInput["facebookProfileURL"] != "#" && $formInput["facebookProfileURL"] != "")
        {
            $base64 = $this->convertPathToImage($formInput["facebookProfileURL"]);
            $formInput["cameraInput"] = $base64;
        }

        Session::put("formInput", $formInput);
        Session::put("signatureAcquired", true);

        if(isset($formInput['facebookToken']))
        {
            $formInput['facebookToken'] = CS_API::extendFacebookToken($formInput['facebookToken']);
        }

        $settings = Session::get("settings");
        $clubSpeedCustomerData = array(
            "birthdate" => isset($formInput["birthdate"]) ? $formInput["birthdate"] . "T00:00:00": "",
            "mobilephone" => isset($formInput["mobilephone"]) ? $formInput["mobilephone"] : "",
            "howdidyouhearaboutus" => isset($formInput["howdidyouhearaboutus"]) ? $formInput["howdidyouhearaboutus"] : "",
            "firstname" => isset($formInput["firstname"]) ? ucfirst($formInput["firstname"]) : "",
            "lastname" => isset($formInput["lastname"]) ? ucfirst($formInput["lastname"]) : "",
            "racername" => isset($formInput["racername"]) ? $formInput["racername"] : "",
            "email" => isset($formInput["email"]) ? $formInput["email"] : "",
            "donotemail" => isset($formInput["consenttoemail"]) ? (!$formInput["consenttoemail"]) : "",
            "profilephoto" => isset($formInput["cameraInput"]) ? $formInput["cameraInput"] : "",
            "signaturephoto" => isset($formInput["signature"]) ? $formInput["signature"] : "",
            "gender" => isset($formInput["gender"]) ? $formInput["gender"] : "other",
            "BusinessName" => isset($settings["BusinessName"]) ? $settings["BusinessName"] : "",
            "Waiver1" => isset($settings["Waiver1"]) ? $settings["Waiver1"] : "",
            "Waiver2" => isset($settings["Waiver2"]) ? $settings["Waiver2"] : "",
            "isMinor" => Session::has('isMinor') ? Session::get("isMinor") : false,
            "Address" => isset($formInput["Address"]) ? $formInput["Address"] : "",
            "Address2" => isset($formInput["Address2"]) ? $formInput["Address2"] : "",
            "Country" => isset($formInput["Country"]) ? $formInput["Country"] : "",
            "City" => isset($formInput["City"]) ? $formInput["City"] : "",
            "State" => isset($formInput["State"]) ? $formInput["State"] : "",
            "Zip" => isset($formInput["Zip"]) ? $formInput["Zip"] : "",
            "Custom1" => isset($formInput["Custom1"]) ? $formInput["Custom1"] : "",
            "Custom2" => isset($formInput["Custom2"]) ? $formInput["Custom2"] : "",
            "Custom3" => isset($formInput["Custom3"]) ? $formInput["Custom3"] : "",
            "Custom4" => isset($formInput["Custom4"]) ? $formInput["Custom4"] : "",
            "eventId" => isset($formInput["eventgroupid"]) ? $formInput["eventgroupid"] : "-1",
            "LicenseNumber" => isset($formInput["LicenseNumber"]) ? $formInput["LicenseNumber"] : "",
            'facebookId' => isset($formInput['facebookId']) ? $formInput['facebookId'] : "",
            'facebookToken' => isset($formInput['facebookToken']) ? $formInput['facebookToken'] : "",
            'facebookAllowEmail' => isset($formInput['facebookAllowEmail']) ? $formInput['facebookAllowEmail'] : "",
            'facebookAllowPost' => isset($formInput['facebookAllowPost']) ? $formInput['facebookAllowPost'] : "",
            'facebookEnabled' => isset($formInput['facebookEnabled']) ? $formInput['facebookEnabled'] : "",
        );

        //Useful debugging output

        /*$settings = Session::get("settings");
        echo '<h1>Data sent to API</h1>';
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
        echo '<b>Business Name: </b>' . $settings["BusinessName"] . '<br/>';
        echo '<b>Waiver1</b> ' . $settings["Waiver1"] . '<br/>';
        //echo '<b>Facebook profile URL: </b>' . $formInput["facebookProfileURL"] . '<br/>';
        echo '<b>Profile photo:</b> <br/><img src="'.  $clubSpeedCustomerData["profilephoto"]  . '"><br/>';
        echo '<b>Signature photo:</b> <br/><img src="'.  $clubSpeedCustomerData["signaturephoto"]  . '"><br/>';
        echo '<b>Address: </b>' . $clubSpeedCustomerData["Address"] . '<br/>';
        echo '<b>Address2: </b>' . $clubSpeedCustomerData["Address2"] . '<br/>';
        echo '<b>Country: </b>' . $clubSpeedCustomerData["Country"] . '<br/>';
        echo '<b>City: </b>' . $clubSpeedCustomerData["City"] . '<br/>';
        echo '<b>State: </b>' . $clubSpeedCustomerData["State"] . '<br/>';
        echo '<b>Zip: </b>' . $clubSpeedCustomerData["Zip"] . '<br/>';
        echo '<b>Custom1: </b>' . $clubSpeedCustomerData["Custom1"] . '<br/>';
        echo '<b>Custom2: </b>' . $clubSpeedCustomerData["Custom2"] . '<br/>';
        echo '<b>Custom3: </b>' . $clubSpeedCustomerData["Custom3"] . '<br/>';
        echo '<b>Custom4: </b>' . $clubSpeedCustomerData["Custom4"] . '<br/>';
        //echo '<b>EventID: </b>' . $clubSpeedCustomerData["EventID"] . '<br/>';
        echo '<b>LicenseNumber: </b>' . $clubSpeedCustomerData["LicenseNumber"] . '<br/>';
        die();*/

        $result = CS_API::call("registerCustomer",$clubSpeedCustomerData);

        if ($result === false) //If the call failed
        {
            CS_API::log('ERROR :: Call to /register failed! errorInfo: ' . print_r(Session::get('errorInfo'),true));
            Session::put('registrationStatus','failed');
            return Redirect::to('/disconnected'); //Redirect to an error page
        }

        Session::put('registrationStatus','complete');
        return Redirect::to('/step4');
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

        return $base64;
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
} 