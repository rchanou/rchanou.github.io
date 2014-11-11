<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class Step1Controller
 * View: step1.blade.php
 * URL: /step1
 *
 * Description:
 * This controller grabs the information needed to display the "See the Lineup" search.
 * It also queries Club Speed for Online Booking's current settings.
 */
class Step1Controller extends BaseController
{
    public function entry()
    {
        $settings = Settings::getSettings(true); //Update website settings
        Session::put('settings',$settings); //and record them in the session

        $strings = Strings::getStrings(); //Update website strings

        $maxRacers = Config::get('config.maxRacers'); //Used to determine "How many drivers?" dropdown max range

        $heatTypesAvailable = CS_API::getAvailableBookingsForDropdown(); //Get a list of all available booking types to choose from

        if ($heatTypesAvailable === null) //If there was an error with the API call, redirect to the Disconnected page
        {
            return Redirect::to('/disconnected');
        }

        $heatTypesAvailable = CS_API::filterDropdownHeatsByAvailableSpots($heatTypesAvailable,1); //Only list the ones with at least one spot

        if (Input::has('debug'))
        {
            Session::put('debug',true);
        }
        //Render the page
        return View::make('/steps/step1',
            array(
                'images' => Images::getImageAssets(),
                'heatTypes' => $heatTypesAvailable,
                'maxRacers' => $maxRacers,
                'strings' => $strings
            )
        );
    }
}