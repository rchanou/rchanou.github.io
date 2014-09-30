<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class LoginController
 *
 * This controller handles login logic. This is either as a result of entering a username and password, or logging in
 * via a third-party, like Facebook.
 *
 * In the case of a Facebook login, it will actually automatically create a Club Speed account if necessary.
 * This is unless the server requires manual creation of a Club Speed account the first time a Facebook login is attempted.
 *
 */
class LoginController extends BaseController
{
    /**
     * This function is hit when someone POSTS into /login with:
     * EmailAddress,Password,heatId,numberOfParticipants,source
     *
     * Data validation is performed, and the user is either logged in successfully or redirected with an error.
     * @return mixed
     */
    public function login()
    {
        $input = Input::all();
        $heatId = $input['heatId'];

        //DATA VALIDATION
        $rules = array();
        $rules['EmailAddress'] = 'required|email';
        $rules['Password'] = 'required';

        $messages = array(
            'EmailAddress.required' => 'Your e-mail address is required for login.',
            'EmailAddress.email' => 'Please enter a valid e-mail address.',
            'Password.required' => 'Your password is required for login.'
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
            if (array_key_exists('source',$input) && $input['source'] == "step2")
            {
                $loginToAccountErrors = array();
                $loginToAccountErrors[$input['heatId']] = $validator->errors()->all();
                return Redirect::to("/step2?login=$heatId#$heatId")->with(array('loginToAccountErrors' => $loginToAccountErrors));
            }
            else
            {
                return Redirect::to('/step1')->withErrors($validator);
            }
        }

        //Attempt to login
        $loginResults = CS_API::loginToClubSpeed($input['EmailAddress'],$input['Password']);

        if ($loginResults != null) //If the login call did not encounter an error
        {
            //If a customerId is received, they're logged in.
            if (property_exists($loginResults,'customerId') && is_numeric($loginResults->customerId))
            {
                //If success, if a booking is being attempted, direct to booking call
                $customerId = $loginResults->customerId;

                //Set the user as logged in
                Session::put('authenticated',$loginResults->customerId);
                Session::put('authenticatedEmail',$input['EmailAddress']);

                //Direct them to the URL that will add that item to their cart
                $heatId = $input['heatId'];
                $numOfRacers = $input['numberOfParticipants'];

                return Redirect::to("/cart?action=add&heatId=$heatId&quantity=$numOfRacers");
            }
            else //If error, redirect backwards appropriately (return destination may vary)
            {
                if (array_key_exists('source',$input) && $input['source'] == "step2")
                {
                    $loginToAccountErrors = array();
                    $loginToAccountErrors[$input['heatId']] = array("Incorrect username or password.");
                    return Redirect::to("/step2?login=$heatId#$heatId")->with(array('loginToAccountErrors' => $loginToAccountErrors));
                }
                else
                {
                    $messages = new Illuminate\Support\MessageBag;
                    $messages->add('errors', "Incorrect username or password.");
                    return Redirect::to('/step1')->withErrors($messages)->withInput();
                }
            }
        }
        else  //If the call itself failed
        {
            return Redirect::to('/disconnected'); //Report the failure
        }
    }

    /**
     * This function handles rendering the page that Facebook posts back to upon an attempt to login to Facebook.
     * We extract the desired heatId to book and quantity, and then render the page, which handles client-side
     * determining if the user successfully logged in via Facebook. The page will then immediately post into loginFacebookConfirm below.
     * @return mixed
     */
    public function loginFacebook()
    {
        if (!Input::has('code')) //If the URL wasn't visited as the result of a Facebook login
        {
            return Redirect::to("/step1"); //Go back to the home page
        }
        else
        {
            $lastSearch = explode('!',Input::get('state')); //Extracting the desired heat and quantity from the return URL - state is a URL parameter Facebook provides for data transfer
            $heatId = $lastSearch[0];
            $quantity = $lastSearch[1];

            return View::make('/loginfb',
                array(
                    'images' => Images::getImageAssets(),
                    'heatId' => $heatId,
                    'quantity' => $quantity
                )
            );
        }
    }

    /**
     * This function handles the POSTing of the result of the user's attempt to log into the online booking site via Facebook.
     * It acquires their information and attempts to log in using their Facebook credentials.
     * By default, a new account is created for them behind the scenes if they don't have one yet.
     * However, if forceRegistrationIfAuthenticatingViaThirdParty is enabled, they are forced to create their own account
     * if they do not have a claimed account yet.
     * @return mixed
     */
    public function loginFacebookConfirm()
    {
        $input = Input::all();
        $customerData = array(
            'facebookId' => $input['facebookId'],
            'facebookToken' => $input['facebookToken'],
            'facebookAllowEmail' => $input['facebookAllowEmail'],
            'facebookAllowPost' => $input['facebookAllowPost'],
            'facebookEnabled' => $input['facebookEnabled'],
            'email' => $input['email'],
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'birthdate' => $input['birthdate'],
            'gender' => $input['gender']
        );

        if (isset($input['facebookToken'])) //If we have the temporary Facebook token
        {
            $input['facebookToken'] = CS_API::extendFacebookToken($customerData['facebookToken']); //Try to exchange it for the 60-day one
        }

        $heatId = $input['heatId'];

        //If the site requires registration, force it on the user, otherwise log them in
        $settings = Session::get('settings');
        $facebookLoginRequiresAccountClaiming = $settings['forceRegistrationIfAuthenticatingViaThirdParty'];
        if ($facebookLoginRequiresAccountClaiming)
        {
            $isAccountClaimedYet = CS_API::isAccountClaimed($customerData['email']);
            if ($isAccountClaimedYet === null)
            {
                return Redirect::to('/disconnected');
            }
            if (is_bool($isAccountClaimedYet) && !$isAccountClaimedYet)
            {
                $createAccountErrors = array();
                $createAccountErrors[$input['heatId']] = array("This track requires you to create an account before using Facebook login. Please create one below using the same e-mail address as your Facebook account.");
                return Redirect::to("/step2?create=$heatId#$heatId")->with(array('createAccountErrors' => $createAccountErrors));
            }
        }

        $loginResults = CS_API::loginToClubSpeedViaFacebook($customerData); //Otherwise, just log them in via Facebook, creating an account behind the scenes if necessary

        //If a customerId is received, they're logged in.
        if (is_numeric($loginResults))
        {
            //If success, if a booking is being attempted, direct to booking call
            $customerId = $loginResults;

            //Set the user as logged in
            Session::put('authenticated',$customerId);
            Session::put('authenticatedEmail',$input['email']);

            //Direct them to the URL that will add that item to their cart
            $heatId = $input['heatId'];
            $numOfRacers = $input['quantity'];

            return Redirect::to("/cart?action=add&heatId=$heatId&quantity=$numOfRacers");
        }
        else
        {
            return Redirect::to('/disconnected');
        }
    }
}