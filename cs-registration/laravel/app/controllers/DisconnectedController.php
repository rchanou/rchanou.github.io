<?php

require_once(app_path().'/tools/CS_API.php');
require_once(app_path().'/config/resources/strings.php');
require_once(app_path().'/config/resources/images.php');
require_once(app_path().'/config/resources/settings.php');

class DisconnectedController extends BaseController {

    //####################################
    //# FAILURE TO CONNECT TO CLUB SPEED #
    //####################################

    /**
     * This page is displayed if there is any difficulty contacting Club Speed.
     * An error message is shown to the user, and they are encouraged to try again or to
     * contact Club Speed support.
     *
     * @return mixed This creates the actual view: disconnected.blade.php
     */
    public function disconnected()
    {
        //Define defaults and create the view
        $session['strings'] = Strings::getDefaultEnglish();
        $session['strings']["cultureNames"] = Strings::getCultureNames();
        $session['images'] = Images::getDefaultImages();
        $session['settings'] = Settings::getDefaultSettings();
        $session['currentCulture'] = "en-US";
        $session['currentCultureFB'] = "en_US";
        $session['translations'] = array("en-US" => $session['strings']);

        return View::make('/steps/disconnected', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB'],
            'step1URL' => CS_API::getStep1URL()));
    }
} 