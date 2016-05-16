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
        $currentCulture = Session::get('currentCulture');
        $translations = Session::get('translations');
        $strings = Strings::getStrings();
        $settings = Session::get('settings');
        $view = '/success';
        if (isset($settings['responsive']) && $settings['responsive'] == true)
        {
            $view = '/success-responsive';
        }

        //Clear the session...
        Session::flush();

        //...but keep the user logged in and basic string data intact
        Session::put('authenticated',$authenticatedUserId);
        Session::put('authenticatedEmail',$authenticatedEmail);
        Session::put('currentCulture',$currentCulture);
        Session::put('translations',$translations);
        Strings::setStrings($strings);



        return View::make($view,
            array(
                'images' => Images::getImageAssets(),
                'check' => $successResults['check'],
                'checkId' => $successResults['checkId'],
                'paymentInformation' => $successResults['paymentInformation'],
                'strings' => Strings::getStrings()
            )
        );
    }
} 