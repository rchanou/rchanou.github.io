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

    //People can get here trying to add an item to cart.
    //Either an actual heat or a merchandise product or a gift card.
    //They can also just visit it directly via a link, like trying to visit the cart page without any specific intent.
    public function loginEntry()
    {
        if (Session::has('authenticated'))
        {
            return Redirect::to('/cart');
        }
        $intent = Session::get('intent');
        if ($intent == null)
        {
            $intent = array('action' => null,
                'heatId' => null,
                'productId' => null,
                'quantity' => null);

        }
        return View::make('/login',
            array(
                'images' => Images::getImageAssets(),
                'settings' => Session::get('settings'),
                'intent' => $intent,
                'strings' => Strings::getStrings()
            )
        );
    }

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
        $strings = Strings::getStrings();
        $heatId = isset($input['heatId']) ? $input['heatId'] : null; //Heat that the user intends to book after creating the account
        $productId = isset($input['productId']) ? $input['productId'] : null; //Product that the user intends to book after creating the account
        $source = isset($input['pageSource']) ? $input['pageSource'] : 'step1'; //Where the user came from and should be redirected to in the case of an error
        $itemId = $heatId != null ? $heatId : $productId; //The heatId or productId being booked
        $quantity = isset($input['numberOfParticipants']) ? $input['numberOfParticipants'] : (isset($input['quantity']) ? $input['quantity'] : 1);

        //DATA VALIDATION
        $rules = array();
        $rules['EmailAddress'] = 'required|email';
        $rules['Password'] = 'required';

        $messages = array(
            'EmailAddress.required' => $strings['str_email.required'],
            'EmailAddress.email' => $strings['str_email.email'],
            'Password.required' => $strings['str_password.required']
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
            if ($source == "step2")
            {
                $loginToAccountErrors = array();
                $loginToAccountErrors[$itemId] = $validator->errors()->all();
                return Redirect::to("/step2?login=$itemId#$itemId")->with(array('loginToAccountErrors' => $loginToAccountErrors));
            }
            else if ($source == "giftcards")
            {
                $loginToAccountErrors = array();
                $loginToAccountErrors[$itemId] = $validator->errors()->all();
                return Redirect::to("/giftcards?login=$itemId#$itemId")->with(array('loginToAccountErrors' => $loginToAccountErrors));
            }
            else if ($source == "login")
            {
                return Redirect::to('/login')->withErrors($validator);
            }
            else
            {
                return Redirect::to('/step1')->withErrors($validator);
            }
        }

        //Attempt to login
        $loginResults = CS_API::loginToClubSpeed($input['EmailAddress'],$input['Password']);

        if ($loginResults !== null) //If the login call did not encounter an error
        {
            //If a customerId is received, they're logged in.
            if ($loginResults !== false && property_exists($loginResults,'customerId') && is_numeric($loginResults->customerId))
            {
                //If success, if a booking is being attempted, direct to booking call
                $customerId = $loginResults->customerId;

                //Set the user as logged in
                Session::put('authenticated',$loginResults->customerId);
                Session::put('authenticatedEmail',$input['EmailAddress']);

                if ($heatId != null)
                {
                    return Redirect::to("/cart?action=add&heatId=$itemId&quantity=$quantity");
                }
                else if ($productId != null)
                {
                    return Redirect::to("/cart?action=add&productId=$itemId&quantity=$quantity");
                }
                else
                {
                    return Redirect::to("/cart");
                }
            }
            else //If error, redirect backwards appropriately (return destination may vary)
            {
                if ($source == "step2")
                {
                    $loginToAccountErrors = array();
                    $loginToAccountErrors[$itemId] = array($strings['str_incorrectUsernameOrPassword']);
                    return Redirect::to("/step2?login=$itemId#$itemId")->with(array('loginToAccountErrors' => $loginToAccountErrors));
                }
                else if ($source == "login")
                {
                    $messages = new Illuminate\Support\MessageBag;
                    $messages->add('errors', $strings['str_incorrectUsernameOrPassword']);
                    return Redirect::to('/login')->withErrors($messages);
                }
                else if ($source == "giftcards")
                {
                    $loginToAccountErrors = array();
                    $loginToAccountErrors[$itemId] = array($strings['str_incorrectUsernameOrPassword']);
                    return Redirect::to("/giftcards?login=$itemId#$itemId")->with(array('loginToAccountErrors' => $loginToAccountErrors));
                }
                else
                {
                    $messages = new Illuminate\Support\MessageBag;
                    $messages->add('errors', $strings['str_incorrectUsernameOrPassword']);
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
            $heatId = null;
            $productId = null;
            $quantity = 1;
            $source = 'step2';


            $action = Input::get('state'); //Extracting the desired heat and quantity from the return URL - state is a URL parameter Facebook provides for data transfer
            //state=heatId!quantity OR state=productId|quantity - the ! or | determine the type of item

            $isAHeatItem = false;
            if (isset($action))
            {
                $isAHeatItem = strpos($action,'!') !== false;
            }

            if ($isAHeatItem)
            {
                $lastSearch = explode('!',$action);
                $heatId = $lastSearch[0];
                $quantity = $lastSearch[1];
                $source = 'step2';
            }
            else
            {
                $lastSearch = explode('|',$action);
                $productId = $lastSearch[0];
                $quantity = $lastSearch[1];
                $source = 'giftcards'; //Some day will need to be differentiated from other products
            }

            return View::make('/loginfb',
                array(
                    'images' => Images::getImageAssets(),
                    'heatId' => $heatId,
                    'productId' => $productId,
                    'pageSource' => $source,
                    'quantity' => $quantity,
                    'strings' => Strings::getStrings()
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
        $strings = Strings::getStrings();
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

        $heatId = isset($input['heatId']) ? $input['heatId'] : null; //Heat that the user intends to book after creating the account
        $productId = isset($input['productId']) ? $input['productId'] : null; //Product that the user intends to book after creating the account
        $itemId = $heatId != null ? $heatId : $productId; //The heatId or productId being booked
        $source = isset($input['pageSource']) ? $input['pageSource'] : 'step2'; //Where the user came from and should be redirected to in the case of an error
        $quantity = isset($input['quantity']) ? $input['quantity'] : 1;

        if ($input['email'] == '' || $input['email'] == null) //If no valid e-mail was received from Facebook, we must have the user create a new account
        {
            if (Session::has('intent'))
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_problemWithFacebook']);
                return Redirect::to('/login')->withErrors($messages);
            }
            else
            {
                $createAccountErrors = array();
                $createAccountErrors[$itemId] = array($strings['str_problemWithFacebook']);
                return Redirect::to("/$source?create=$itemId#$itemId")->with(array('createAccountErrors' => $createAccountErrors));
            }
        }

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
                if (Session::has('intent')) //A login requested was forced on the user in the middle of trying to add an item to cart
                {
                    $messages = new Illuminate\Support\MessageBag;
                    $messages->add('errors', $strings['str_accountCreationForced']);
                    return Redirect::to('/login')->withErrors($messages);
                }
                else
                {
                    $createAccountErrors = array();
                    $createAccountErrors[$itemId] = array($strings['str_accountCreationForced']);
                    return Redirect::to("/$source?create=$itemId#$itemId")->with(array('createAccountErrors' => $createAccountErrors));
                }
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
            if ($heatId != null)
            {
                return Redirect::to("/cart?action=add&heatId=$itemId&quantity=$quantity");
            }
            else if ($productId != null)
            {
                return Redirect::to("/cart?action=add&productId=$itemId&quantity=$quantity");
            }
            else
            {
                return Redirect::to("/cart");
            }
        }
        else //If Facebook login failed, have the user just create a new account
        {
            if (Session::has('intent'))
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_problemWithFacebook']);
                return Redirect::to('/login')->withErrors($messages);
            }
            else
            {
                $createAccountErrors = array();
                $createAccountErrors[$itemId] = array($strings['str_problemWithFacebook']);
                return Redirect::to("/$source?create=$itemId#$itemId")->with(array('createAccountErrors' => $createAccountErrors));
            }
        }
    }
}