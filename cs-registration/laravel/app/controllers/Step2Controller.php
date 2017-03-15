<?php

require_once(app_path().'/tools/CS_API.php');
require_once(app_path().'/config/resources/strings.php');
require_once(app_path().'/config/resources/images.php');
require_once(app_path().'/config/resources/settings.php');

//##############################
//# STEP 2 - REGISTRATION FORM #
//##############################

class Step2Controller extends BaseController {
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
        CS_API::checkForLanguageChange();
        if(CS_API::sessionIsInvalid())
        {
            return Redirect::to(CS_API::getStep1URL());
        }

        $session = Session::all();

        $oldInput = null;
        if (Session::has("oldInput")) //Restore previous input if present
        {
            $oldInput = Session::get("oldInput");
            Session::forget("oldInput");

            return Redirect::to('/step2')->withInput($oldInput);
        }

        return View::make('/steps/step2',
            array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB'],
            'step1URL' => CS_API::getStep1URL()
            )
        );
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
        $strings = $session['strings'];
        $settings = $session['settings'];
        $input = Input::all();
        if (!array_key_exists('isMinor',$input))
        {
            $input['isMinor'] = false;
        }
        Session::put('isMinor',$input['isMinor']);
        $session['isMinor'] = $input['isMinor'];

        if (isset($input['screenSize'])) //Used for resizing signature pads
        {
            Session::put('screenSize',$input['screenSize']);
        }

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
        if ($settings['CfgRegSrcReq'] && $settings['CfgRegSrcShow'])
        {

            if (!Input::has('howdidyouhearaboutus') || Input::get('howdidyouhearaboutus') == "0")
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_howDidYouHearAboutUs_Missing']);
                return Redirect::to('/step2')->withErrors($messages)->withInput();
            }
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
            if ($settings['zipValidated'])
            {
                $rules['Zip'] = 'validzipcode';

                //Custom Zip validator - either 5 numerical digits, or 9 digit format (11111-4444)
                Validator::extend('validzipcode', function($attribute, $value, $parameters) {
                    $zipIsValid = true;
                    $zip = Input::get('Zip');
                    if (strlen($zip) <= 0)
                    {
                        $zipIsValid = false;
                    }
                    else if (strlen($zip) == 5)
                    {
                        if (!ctype_digit($zip))
                        {
                            $zipIsValid = false;
                        }
                    }
                    else if (strlen($zip) == 10)
                    {
                        $zipFirstFive = substr($zip,0,5);
                        $zipDash = substr($zip,5,1);
                        $zipLastFour = substr($zip,-4);
                        if (!ctype_digit($zipFirstFive) || !ctype_digit($zipLastFour) || $zipDash != '-')
                        {
                            $zipIsValid = false;
                        }
                    }
                    else
                    {
                        $zipIsValid = false;
                    }
                    return $zipIsValid;
                });
            }
            else
            {
                $rules['Zip'] = 'required';
            }
        }
        if ($settings['cfgRegCustTxt1Show'] && $settings['cfgRegCustTxt1req'])
        {
            $rules['Custom1'] = 'required';
        }
        if ($settings['cfgRegCustTxt2Show'] && $settings['cfgRegCustTxt2req'])
        {
            $rules['Custom2'] = 'required';
        }
        if ($settings['cfgRegCustTxt3Show'] && $settings['cfgRegCustTxt3req'])
        {
            $rules['Custom3'] = 'required';
        }
        if ($settings['cfgRegCustTxt4Show'] && $settings['cfgRegCustTxt4req'])
        {
            $rules['Custom4'] = 'required';
        }
        if ($settings['CfgRegDrvrLicShow'] && $settings['CfgRegDrvrLicReq'])
        {
            $rules['LicenseNumber'] = 'required';
        }
        if ($settings['CfgRegEmailShow'] && !($input['isMinor'] && $settings['CfgRegDisblEmlForMinr']))
        {
            if ($settings['CfgRegEmailReq'] && (!array_key_exists('Country',$input) ||
                    (array_key_exists('Country',$input) && $input['Country'] != 'Canada') ))
            {
                $rules['email'] = 'required|email';
            }
            else
            {
                $rules['email'] = 'email';
            }
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
                CS_API::log('ERROR :: API call to search for a duplicate e-mail failed! errorInfo: ' . print_r(Session::get('errorInfo'),true));
                return Redirect::to('/disconnected'); //Redirect to an error page
            }
            $isEmailTaken = count($searchByEmail["racers"]) > 0;
        }

        if ($isEmailTaken)
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_emailAlreadyRegistered']);
            return Redirect::to('/step2')->withErrors($messages)->withInput();
        }

        if (isset($input['tooYoungToRegister']) && $input['tooYoungToRegister'])
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_step2SubmitCannot']);
            return Redirect::to('/step2')->withErrors($messages)->withInput();
        }

        if (isset($input['eventgroupid']) && $input['eventgroupid'] == 'EventCode')
        {
            $rules['EventCode'] = 'required';
        }

        //Error messages in case of validation failure
        $messages = array(
            'birthdate.required' => $strings['str_birthdate.required'],
            'birthdate.before' => $strings['str_birthdate.before'],
            'birthdate.date' => $strings['str_birthdate.date'],
            'mobilephone.required' => $strings['str_mobilephone.required'],
            'howdidyouhearaboutus.required' => $strings['str_howdidyouhearaboutus.required'],
            'firstname.required' => $strings['str_firstname.required'],
            'lastname.required' => $strings['str_lastname.required'],
            'racername.required' => $strings['str_racername.required'],
            'email.required' => $strings['str_email.required'],
            'email.email' => $strings['str_email.email'],
            'Address.required' => $strings['str_Address.required'],
            'Country.required' => $strings['str_Country.required'],
            'City.required' => $strings['str_City.required'],
            'State.required' => $strings['str_State.required'],
            'Zip.required' => $strings['str_Zip.required'],
            'Custom1.required' => $strings['str_Custom1.required'],
            'Custom2.required' => $strings['str_Custom2.required'],
            'Custom3.required' => $strings['str_Custom3.required'],
            'Custom4.required' => $strings['str_Custom4.required'],
            'LicenseNumber.required' => $strings['str_LicenseNumber.required'],
            'Zip.validzipcode' => $strings['str_invalidZipCode'],
            'EventCode.required' => $strings['str_EventCode.required']
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        // If validation fails, redirect
        if ($validator->fails()) {
            return Redirect::to('/step2')->withErrors($validator)->withInput();
        }

        // If there's an event code, fetch the corresponding eventId if one exists
        if (array_key_exists("EventCode",$input)) {
            $eventsMatchingEventCode = CS_API::getEventGroupsByEventCode($input["EventCode"]);
            if (empty($eventsMatchingEventCode))
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_invalidEventCode']);
                return Redirect::to('/step2')->withErrors($messages)->withInput();
            }
            else
            {
                $input["eventgroupid"] = $eventsMatchingEventCode[0]["eventId"];
            }
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
                if (!$_FILES["cameraInput"]["error"])
                {
                    $input["cameraInput"] = $this->convertPathToImage($_FILES["cameraInput"]["tmp_name"],$fileExtension);
                }
                else //If there was an error, the image was likely too large
                {
                    $input["cameraInput"] = null;
                    $messages = new Illuminate\Support\MessageBag;
                    $messages->add('errors', $strings['str_imageError']);
                    return Redirect::to('/step2')->withErrors($messages)->withInput();

                }

            }
        }
        else if (array_key_exists("cameraInputIPCam_currentSnapshotURL",$input) &&
            $input["cameraInputIPCam_currentSnapshotURL"] != null) //If we're using an IP Camera and have a non-blank picture, use that
        {
            $input["cameraInput"] = $input["cameraInputIPCam_currentSnapshotURL"];

            if (substr($input["cameraInput"],0,5) != 'data:') //If the image isn't in base64 format yet //TODO: Need to detect if need to use base64 instead
            {
                $input["cameraInput"] = $this->convertPathToImage($input["cameraInput"]);
            }
            else
            {
                $input["cameraInput"] = $input["cameraInputIPCam_currentSnapshotBase64"];
            }
        }
        else if (array_key_exists("cameraInputLocalCam_currentSnapshotURL",$input) &&
            $input["cameraInputLocalCam_currentSnapshotURL"] != null) //If we're using an Local Camera and have a non-blank picture, use that
        {
            $input["cameraInput"] = $input["cameraInputLocalCam_currentSnapshotURL"];


            if (substr($input["cameraInput"],0,5) != 'data:') //If the image isn't in base64 format yet
            {
                $input["cameraInput"] = $this->convertPathToImage($input["cameraInput"]);
            }
            else
            {
                $input["cameraInput"] = $input["cameraInputLocalCam_currentSnapshotURL"];
            }
            //echo('<img src="' . $input["cameraInput"] . '">' ); die();
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

        //Strips away EXIF orientation data after rotating, if necessary
        $exifData = @exif_read_data($path);
        if (isset($exifData['Orientation']))
        {
            $possibleRotations = array(0, 0, 0, 180, 0, 0, -90, 0, 90);
            $data = imagecreatefromstring($data);
            $data = imagerotate($data, $possibleRotations[$exifData['Orientation']] ?: 0, 0);
            ob_start();
            imagejpeg($data, NULL, 100);
            $data = ob_get_contents();
            ob_end_clean();
        }

        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
} 