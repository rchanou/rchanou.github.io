<?php

require_once(app_path().'/includes/includes.php');


/**
 * Class LocalizationController
 *
 * This class controls the application's localization.
 */
class LocalizationController extends BaseController {

    /**
     * This function is called when an option in the language dropdown menu is selected.
     * This is achieved by causing the dropdown selection to redirect to /changeLanguage/newLanguageCode/destination
     * It results in a redirect to the step that originated the dropdown change, and lets that step know to change languages.
     * @param string $newLanguageCode The language code to switch to. Ex. "en-US"
     * @param string $destinationStep The step to redirect to. This is the same step that the dropdown was selected from.
     * @return mixed Redirect to the originating step, adding in a culture change request to the session.
     */
    public function changeLanguage($newLanguageCode, $destination)
    {
        return Redirect::to($destination)->withInput()->with('currentCultureChanged',$newLanguageCode);
    }

} 