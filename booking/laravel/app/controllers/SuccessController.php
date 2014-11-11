<?php

require_once(app_path().'/includes/includes.php');

class SuccessController extends BaseController
{
    public function success()
    {
        if (!Session::has('authenticated') || !Session::has('successResults')) //If the user is not logged in and/or hasn't just finished a registration
        {
            return Redirect::to('/step1'); //Send them to the first page
        }
        $successResults = Session::get('successResults');

        $authenticatedUserId = Session::get('authenticated');
        $authenticatedEmail = Session::get('authenticatedEmail');

        //Clear the session...
        Session::flush();
        //Session::regenerate();

        //...but keep the user logged in
        Session::put('authenticated',$authenticatedUserId);
        Session::put('authenticatedEmail',$authenticatedEmail);

        return View::make('/success',
            array(
                'images' => Images::getImageAssets(),
                'check' => $successResults['check'],
                'checkId' => $successResults['checkId'],
                'paymentInformation' => $successResults['paymentInformation']
            )
        );
    }
} 