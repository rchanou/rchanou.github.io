<?php

require_once(app_path().'/includes/includes.php');

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    if( ! Request::secure())
    {
        return Redirect::secure(Request::path());
    }
});

App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('checkIfDisabled', function ()
{
    if (Request::segment(1) != "disabled") //Prevents redirect loops
    {
        $settings = Settings::getSettings(); //Get website settings and check if we should be disabled

        if (Input::has('source') && $settings['brokerSourceInURLEnabled']) //Broker name can be put as a URL parameter 'source'
        {
            Session::put('brokerName',Input::get('source'));
            Session::put('brokerNameSource','url');
        }

        if (Input::has('testMode'))
        {
            if (Input::get('testMode') == 'on')
            {
                Session::put('debug',true);
            }
        }

        $key = Input::get('key'); //Check for a key that may override the disabled state

        if ($key == md5(Config::get('config.privateKey'))) //If the key is correct
        {
            Session::put('disabledOverride',true); //Inform the session not to allow the site to be disabled
        }

        if (!class_exists('NumberFormatter'))
        {
            Session::put('errorInfo', 'Needs php_intl.dll migration run!');
            return Redirect::to('/disabled');

        }

        $paymentProcessor = ($settings['onlineBookingPaymentProcessorSettings']);
        $paymentProcessor = $paymentProcessor->name;
        if (!$settings['registrationEnabled'] && Session::get('disabledOverride') != true
            || ($settings['registrationEnabled'] && $paymentProcessor == "Dummy" && Session::get('disabledOverride') != true))
        {
            return Redirect::to('/disabled');
        }
    }

});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
