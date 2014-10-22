<?php

require_once(app_path().'/tools/CS_API.php');
require_once(app_path().'/config/resources/strings.php');
require_once(app_path().'/config/resources/images.php');
require_once(app_path().'/config/resources/settings.php');

class CheckInController extends BaseController
{
    /**
     * This function just returns the main Check In page, which asks the user for their first name, last name, and birth date.
     * @return mixed The actual view, /steps/checkin
     */
    public function checkIn()
    {
        CS_API::checkForLanguageChange(); //Check for a language change
        if(CS_API::sessionIsInvalid()) //If the session is invalidated, redirect to step1 while maintaining any ip camera settings
        {

            $step1URL = '/step1';
            if (Session::has('ipcam'))
            {
                $step1URL = $step1URL . '?&terminal=' . Session::get('ipcam');
            }
            return Redirect::to($step1URL);
        }

        $session = Session::all();

        return View::make('/steps/checkin',
            array('strings' => $session['strings'],
                'images' => $session['images'],
                'settings' => $session['settings'],
                'translations' => $session['translations'],
                'currentCulture' => $session['currentCulture'],
                'currentCultureFB' => $session['currentCultureFB']
            )
        );
    }

    /**
     * This function is called when the user submits the form with their first name, last name, and birth date
     * while trying to check-in with an existing account.
     *
     * Their data is validated. On failure, they are returned to the main check-in page with an error.
     * On success, Club Speed is sent their information.
     * If they have an account, I retain their customerID and continue to the "check in with FB" page.
     * If they do not have an account, they are redirected with an error.
     *
     * @return mixed
     */
    public function postCheckIn()
    {
        //Perform basic validation
        $input = Input::all();
        $session = Session::all();
        $strings = $session['strings'];

        //Rules for validation
        $rules = array();
        $rules['birthdate'] = 'required|before:today|date';
        $rules['firstname'] = 'required';
        $rules['lastname'] = 'required';

        //Error messages in case of validation failure
        $messages = array(
            'birthdate.required' => $strings['str_birthdate.required'],
            'birthdate.before' => $strings['str_birthdate.before'],
            'birthdate.date' => $strings['str_birthdate.date'],
            'firstname.required' => $strings['str_firstname.required'],
            'lastname.required' => $strings['str_lastname.required'],
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        // If validation fails, redirect
        if ($validator->fails()) {
            return Redirect::to('/checkin')->withErrors($validator)->withInput();
        }
        //--END VALIDATION

        $customer = CS_API::getExistingCustomerMatching($input['firstname'],$input['lastname'],$input['birthdate']); //Get the customer ID matching the credentials provided

        if ($customer === null) //If there was an error with the API call
        {
            return Redirect::to('/disconnected'); //Redirect to an error page
        }
        else if ($customer === false) //If no customer was found matching the fields, report an error and redirect
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_checkInFailure']);
            return Redirect::to('/checkin')->withErrors($messages)->withInput();
        }

        Session::put('checkInUserID',$customer['customerId']); //Record the user's ID in the session
        Session::put('checkInUserEmail',$customer['email']); //Record the user's e-mail in the session

        if (!$session['settings']['Reg_EnableFacebook'])
        {
            return Redirect::to('checkinconfirm');
        }
        return View::make('/steps/checkinfb',
            array('strings' => $session['strings'],
                'images' => $session['images'],
                'settings' => $session['settings'],
                'translations' => $session['translations'],
                'currentCulture' => $session['currentCulture'],
                'currentCultureFB' => $session['currentCultureFB']
            )
        );
    }

    /**
     * This page just renders the check in confirmation page, where users select which event they're attending.
     * Users must have had an account successfully found with the first name, last name, and birth date prior to
     * reaching this page.
     * User may reach this page directly, or via a redirect from Facebook which allows us access to their Facebook token.
     * @return mixed
     */
    public function checkInConfirm()
    {
        CS_API::checkForLanguageChange(); //Check for a language change
        if(CS_API::sessionIsInvalid()) //If the session is invalidated, redirect to step1 while maintaining any ip camera settings
        {

            $step1URL = '/step1';
            if (Session::has('ipcam'))
            {
                $step1URL = $step1URL . '?&terminal=' . Session::get('ipcam');
            }
            return Redirect::to($step1URL);
        }

        $session = Session::all();

        //Redirect back if the 'checkInUserID' is not in session
        if (!Session::has('checkInUserID'))
        {
            $step1URL = '/step1';
            if (Session::has('ipcam'))
            {
                $step1URL = $step1URL . '?&terminal=' . Session::get('ipcam');
            }
            return Redirect::to($step1URL);
        }

        return View::make('/steps/checkinconfirm',
            array('strings' => $session['strings'],
                'images' => $session['images'],
                'settings' => $session['settings'],
                'translations' => $session['translations'],
                'currentCulture' => $session['currentCulture'],
                'currentCultureFB' => $session['currentCultureFB']
            )
        );

    }

    /**
     * The user enters this page after submitting which event they're booking for.
     * This information, and possibly their facebook token, is sent to Club Speed.
     * Upon success, we redirect to the success page.
     */
    public function postCheckInFinal()
    {
        //Two separate calls? One for Facebook, one for check-in? Needs to be put in queue.

        $input = Input::all();
        $userEmail = Session::get('checkInUserEmail');
        $userID = Session::get('checkInUserID');

        //If input is missing (should be impossible)
        if (!isset($input['facebookId']) || !isset($input['facebookToken']) || !isset($input['eventgroupid']) )
        {
            return Redirect::to('/checkinconfirm'); //Redirect to the previous page
        }

        //If the user decided to integrate Facebook and has an e-mail address
        if ($input['facebookId'] != "" && $input['facebookToken'] != "" && $userEmail != "" && $userEmail != null)
        {
            //Update their Facebook token with Club Speed
            CS_API::updateFacebookToken($userEmail,$input['facebookId'],$input['facebookToken']);
        }

        //Add the user to the queue (the API will ignore this if their Club Speed version doesn't support clearing the queue cache)
        CS_API::addUserToQueue($userID,$input['eventgroupid']);

        Session::put("signatureAcquired",true); //HACK: Success page expects this value; needs refactoring

        return Redirect::to('step4');
    }
} 