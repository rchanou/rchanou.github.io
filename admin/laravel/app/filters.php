<?php

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
| The following filter checks for an authenticated user and optionally
| sets the return URL
|
*/

Route::filter('auth', function()
{
 	$session = Session::all();
	if (!(isset($session["authenticated"]) && $session["authenticated"]))
	{
			// Set error message
			$messages = new Illuminate\Support\MessageBag;
			$messages->add('errors', "You must login before viewing the admin panel.");
			
			// Set "redirectTo" so that we can return to the intended spot after logging in
			Session::put('redirectTo', Request::path());

			//Redirect to the login page with an appropriate error message
			return Redirect::to('/login')->withErrors($messages)->withInput();
	}
});

Route::filter('validatePermission', function($route, $request, $permissionRequired = null)
{
		$permissions = Session::get('permissions');

		// Must have the task allowed or a roleId of 1 (Administrator)
		if(!array_key_exists($permissionRequired, $permissions['allowedTasks'])
			&& !array_key_exists(1, $permissions['allowedRoles'])
		)
		{
				// Set error message
				$messages = new Illuminate\Support\MessageBag;
				$messages->add('errors', "You do not have permission to access this portion of the Administration Panel.");
			
				//Redirect to the login page with an appropriate error message
				return Redirect::to('/dashboard')->withErrors($messages);
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