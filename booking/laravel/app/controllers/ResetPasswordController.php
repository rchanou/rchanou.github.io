<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class ResetPasswordController
 *
 * This class handles all resetting password logic.
 */
class ResetPasswordController extends BaseController
{
    /**
     * Just renders the page with a form to enter an e-mail address to request a reset password link.
     *
     * @return mixed
     */
    public function entry()
    {
        return View::make('/resetpassword',
            array(
                'images' => Images::getImageAssets(),
                'strings' => Strings::getStrings()
            )
        );
    }

    /**
     * This receives the POST of the form that requests the password reset link.
     *
     * After validation, a request is sent to Club Speed to generate the password reset link.
     * If successful, Club Speed e-mails the link to the given e-mail address and the page is rendered with a successful
     * request.
     * @return mixed
     */
    public function resetPasswordRequest()
    {
        $input = Input::all();
        $strings = Strings::getStrings();

        $emailAddress = Input::get('EmailAddress');

        //DATA VALIDATION
        $rules = array();
        $rules['EmailAddress'] = 'required|email';

        $messages = array(
            'EmailAddress.required' => $strings['str_email.required'],
            'EmailAddress.email' => $strings['str_email.email'],
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
                return Redirect::to('/resetpassword')->withErrors($validator);
        }

        $resetRequestResult = CS_API::requestPasswordReset($emailAddress);

        if ($resetRequestResult != null)
        {
            $resetRequestSuccessful = ($resetRequestResult == true);
            if ($resetRequestSuccessful)
            {
                return View::make('/resetpassword',
                    array(
                        'images' => Images::getImageAssets(),
                        'resetRequestSuccessful' => true,
                        'strings' => Strings::getStrings()
                    )
                );
            }
            else
            {
                return View::make('/resetpassword',
                    array(
                        'images' => Images::getImageAssets(),
                        'resetRequestSuccessful' => false,
                        'strings' => Strings::getStrings()
                    )
                );
            }
        }
        else  //If error, redirect
        {
            return Redirect::to('/disconnected');
        }
    }

    /**
     * This renders the page that actually allows the user to reset and choose a new password.
     * They are e-mailed a link to this page with a token in the URL.
     * @return mixed
     */
    public function resetPasswordForm()
    {
        $authToken = Input::get('token');
        $email = Input::get('email');
        return View::make('/resetpasswordform',
            array(
                'images' => Images::getImageAssets(),
                'userNeedsToSubmitForm' => true,
                'authToken' => $authToken,
                'email' => $email,
                'strings' => Strings::getStrings()
            )
        );
    }

    /**
     * This receives the POST from the form to reset the password.
     * It issues the reset request to Club Speed, and displays the result to the user.
     * @return mixed
     */
    public function resetPasswordSubmission()
    {
        //TODO: Server-side validation

        $email = Input::get('email');
        $password = Input::get('newpassword');
        $token = Input::get('token');

        $resetPasswordResult = CS_API::resetPassword($email,$password,$token);
        $resetPasswordRequestFailed = ($resetPasswordResult === null);

        if ($resetPasswordRequestFailed)  //If error, redirect
        {
            return Redirect::to('/disconnected');
        }

        return View::make('/resetpasswordform',
            array(
                'images' => Images::getImageAssets(),
                'userNeedsToSubmitForm' => false,
                'resetSuccessful' => $resetPasswordResult,
                'strings' => Strings::getStrings()
            )
        );
    }

    /**
     * This is the link e-mailed to customers requesting to reset their password via an iOS device.
     * It should contain the token as a URL parameter.
     * It should immediately trigger the Club Speed iOS app to open, passing along the token received.
     * @return mixed
     */
    public function resetPasswordiOS()
    {
        $token = Input::has('token') ? Input::get('token') : 'TOKEN_MISSING';

        $urlComponents = explode(".",$_SERVER['HTTP_HOST']);
        $trackName = $urlComponents[0];
        $url = "$trackName://token/$token";

        return View::make('/resetpassword_ios',
            array(
                'url' => $url
            )
        );
    }

    /**
     * This is the link e-mailed to customers requesting to reset their password via an Android device.
     * It should contain the token as a URL parameter.
     * It should immediately trigger the Club Speed Android app to open, passing along the token received.
     * @return mixed
     */
    public function resetPasswordAndroid()
    {
        $token = Input::has('token') ? Input::get('token') : 'TOKEN_MISSING';

        $urlComponents = explode(".",$_SERVER['HTTP_HOST']);
        $trackName = $urlComponents[0];
        $url = "intent://token/$token#Intent;scheme=$trackName;package=com.clubspeedtiming.$trackName;end";
        return View::make('/resetpassword_android',
            array(
                'url' => $url
            )
        );
    }
} 