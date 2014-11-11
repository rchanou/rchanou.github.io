<?php
require_once(app_path().'/includes/includes.php');

class LoginController extends BaseController
{
    public function loginStart()
    {
        if(Session::get('authenticated'))
        {
            return Redirect::to('/dashboard');
        }
        return View::make('/login');
    }

    public function loginSubmit()
    {
        $input = Input::all();
        $this->validateLoginInput($input); //TODO: Live validation

        $username = $input['username'];
        $password = $input['password'];

        $loginWasSuccessful = CS_API::login($username,$password);
        $loginCallSucceeded = ($loginWasSuccessful !== null);

        if ($loginCallSucceeded)
        {
            if ($loginWasSuccessful)
            {
                Session::put('authenticated',true);
                Session::put('user',$username);

                $session = Session::all();
                if (isset($session["redirectTo"]))
                {
                    Session::forget("redirectTo");
                    return Redirect::to($session["redirectTo"]);
                }
                return Redirect::to('/dashboard');
            }
            else //If the login attempt failed
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', "Incorrect username or password.");

                //Redirect to the previous page with an appropriate error message
                return Redirect::to('/login')->withErrors($messages)->withInput();
            }
        }
        else //If the login call itself failed
        {
            //TODO: Change this to redirect to a Disconnected page, like other recent Laravel projects
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "There was a problem with your login. Please try again later.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }
        return View::make('/login');
    }

    public function logout()
    {
        Session::flush();
        Session::regenerate();
        return Redirect::to('/login');
    }

    //TODO: I don't think this pattern works in Laravel. The redirect may never happen as it needs to chain up.
    private function validateLoginInput($input)
    {
        $rules = array(
            'username' => 'required',
            'password' => 'required',
        );
        $messages = array(
            'username.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('/login')->withErrors($validator)->withInput();
        }
    }
} 