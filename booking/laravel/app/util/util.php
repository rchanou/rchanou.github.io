<?php
//Returns true if a remote file exists, false otherwise
function remoteFileExists($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $result = curl_exec($curl);
    $ret = false;
    if ($result !== false) {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $ret = true;
        }
    }
    curl_close($curl);
    return $ret;
}

function checkForCultureChange()
{
    getAndUpdateTranslations();
    if (!Session::has('loadedAtLeastOnce'))
    {
        $settings = Session::get('settings');
        $startingCulture = $settings['currentCulture'];
        
        Session::put("currentCultureChanged",$startingCulture);
        Session::put('loadedAtLeastOnce',true);
    }
    if (Session::has("currentCultureChanged"))
    {
        $newCulture = Session::get("currentCultureChanged");
        $translations = Session::get("translations");

        if (array_key_exists($newCulture, $translations))
        {
            Strings::setStrings($translations[$newCulture]);
            Session::put("currentCulture",$newCulture);
        }
        Session::forget("currentCultureChanged");
    }
}

function getAndUpdateTranslations()
{
    $translationsFormatted = CS_API::getTranslations(); //Get the formatted translations for this app from Club Speed

    if ($translationsFormatted == null) //If translations could not be pulled
    {
        //Apply default language to application
        $stringTranslations = array();
        $stringTranslations["en-US"] = Strings::getDefaultStrings();
        Session::put('currentCulture','en-US');
        Session::put('supportedCultures', array('en-US'));
        Session::put('translations', $stringTranslations);
        Strings::setStrings($stringTranslations["en-US"]);

        //Update Club Speed with all the missing strings
        updateClubSpeedStrings();

        return;
    }

    updateClubSpeedStrings($translationsFormatted); //Update Club Speed's strings if any are missing

    //Determine all supported cultures and replace any missing fields with English fields
    $supportedCultures = array();
    foreach($translationsFormatted as $cultureName => $cultureTranslations)
    {
        $supportedCultures[$cultureName] = $cultureName;
        $translationsFormatted[$cultureName] = array_merge(Strings::getDefaultStrings(),$translationsFormatted[$cultureName]);
    }

    //If English isn't supported, insert our default English strings
    if (!array_key_exists("en-US",$supportedCultures))
    {
        $supportedCultures["en-US"] = "en-US";
        array_push($translationsFormatted,array("en-US" => Strings::getDefaultStrings()));
    }

    Session::put('supportedCultures',$supportedCultures); //Store all supported cultures for future use
    Session::put('translations',$translationsFormatted); //Store all translations for future use

    $settings = Session::get('settings');

    if(!Session::has('currentCulture'))
    {
        Session::put('currentCulture',$settings['currentCulture']);
    }
    if (isset($translationsFormatted[Session::get('currentCulture')]))
    {
        Strings::setStrings($translationsFormatted[Session::get('currentCulture')]);
    }
}

function updateClubSpeedStrings($stringsFromClubSpeed = array())
{
    if (array_key_exists('en-US',$stringsFromClubSpeed)) //Locate English strings, if any
    {
        $stringsFromClubSpeed = $stringsFromClubSpeed['en-US'];
    }
    else
    {
        $stringsFromClubSpeed = array();
    }

    $appStrings = Strings::getDefaultStrings(); //Identify the app's expected English strings
    unset($appStrings['cultureNames']); //But ignore the cultureNames array - don't need it synced up

    $stringsClubSpeedIsMissing = array_diff_key($appStrings,$stringsFromClubSpeed); //See if Club Speed is missing any
    if (count($stringsClubSpeedIsMissing) > 0) //If any strings are missing
    {
        //Format the missing string data as expected by Club Speed's API
        $stringsClubSpeedIsMissingFormatted = array();
        foreach($stringsClubSpeedIsMissing as $stringLabel => $stringValue)
        {
            $stringsClubSpeedIsMissingFormatted[] = array('name' => $stringLabel,
                'namespace' => 'Booking',
                'value' => $stringValue,
                'defaultValue' => $stringValue,
                'culture' => 'en-US',
                'comment' => '');
        }

        //Send Club Speed the missing English strings
        CS_API::updateEnglishStrings($stringsClubSpeedIsMissingFormatted);
    }
}