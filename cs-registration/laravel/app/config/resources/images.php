<?php

/**
 * Class Images
 *
 * This class holds the images that are used throughout the registration website.
 * Default images are included.
 *
 * Ultimately, a global session variable 'images' is assigned to the desired images array.
 * That array is then used to distribute images throughout the website.
 */
class Images {
    private static $defaultImages;

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultImages()
    {
        self::initialize();
        return self::$defaultImages;
    }
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$defaultImages = array(
            'bg_image' => 'images/bg_default.jpg',
            'createAccount' => 'images/new_account.png',
            'createAccountFacebook' => 'images/facebook_connect.png',
            'venueLogo' => 'images/default_header.png',
            'poweredByClubSpeed' => 'images/clubspeed.png',
            'completeRegistration' => 'images/complete_registration.png',
            'disconnected' => 'images/redhelmet_disconnect.png'
        );

        self::$initialized = true;
    }
} 