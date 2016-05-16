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
        $settings = Settings::getSettings(true); //Force a refresh of all settings
        Session::put('settings',$settings);
        checkForCultureChange();

        $maxRacers = $settings['maxRacersForDropdown']; //Used to determine "How many drivers?" dropdown max range

        $heatTypesAvailable = CS_API::getAvailableBookingsForDropdown(); //Get a list of all available booking types to choose from

        if ($heatTypesAvailable === null) //If there was an error with the API call, redirect to the Disconnected page
        {
            return Redirect::to('/disconnected');
        }

        $heatTypesAvailable = CS_API::filterDropdownHeatsByAvailableSpots($heatTypesAvailable,1); //Only list the ones with at least one spot

        //Render the page
        $view = '/steps/step1';
        if (isset($settings['responsive']) && $settings['responsive'] == true)
        {
            $view = '/steps/step1-responsive';
        }
        return View::make($view,
            array(
                'images' => Images::getImageAssets(),
                'heatTypes' => $heatTypesAvailable,
                'maxRacers' => $maxRacers,
                'strings' => Strings::getStrings()
            )
        );
    }
}