<?php

/**
 * Class Images
 *
 * This class maintains the list of all images used by the website, and facilitates their overriding if alternatives are
 * present in /assets in the corresponding folder.
 */
class Images
{
    private static $imageAssets;

    private function __construct() { }
    private static $initialized = false;

    //Returns all image assets for the website
    public static function getImageAssets()
    {
        self::initialize();
        return self::$imageAssets;
    }

    //Sets up default images, and overwrites them if alternatives are present in /assets
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$imageAssets = array(
            'header' => 'images/header.jpg',
            'background' => 'images/background.jpg', //Needs to be overwritten in the CSS file
            'disconnected' => 'images/disconnected.png',
            'success' => 'images/success.png',
            'clubspeed_logo' => 'images/clubspeed_logo.png'
        );

        $assetsURL = 'http://' . $_SERVER['HTTP_HOST'] . 'assets/booking/';
        if (Config::has('config.assetsURL'))
        {
            $assetsURL = Config::get('config.assetsURL');
        }
        $assetsURL = str_replace('http://','https://',$assetsURL);

        if (self::urlExists($assetsURL . '/images/'))
        {
            foreach(self::$imageAssets as $imageName => $imageURL)
            {
                if (self::urlExists($assetsURL . '/' . $imageURL))
                {
                    self::$imageAssets[$imageName] = $assetsURL . '/' . $imageURL;
                }
            }
        }

        self::$initialized = true;
    }

    //A helper function that determines whether or not an image exists at a given URL
    private static function urlExists($imageURL)
    {
        $file_headers = @get_headers($imageURL);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
        else {
            $exists = true;
        }
        return $exists;
    }
}