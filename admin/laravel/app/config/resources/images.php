<?php

//TODO: Clear, concise documentation.

//TODO: This is a copy/paste from the booking project. Need to personalize.
class Images
{
    private static $imageAssets;

    private function __construct() { } //Static class emulation in PHP
    private static $initialized = false; //

    public static function getImageAssets()
    {
        self::initialize();
        return self::$imageAssets;
    }
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$imageAssets = array(
            'header' => 'images/header.png',
            'background' => 'images/background.jpg',
            'disconnected' => 'images/disconnected.png'
        );

        $assetsURL = 'http://' . $_SERVER['HTTP_HOST'] . 'assets/admin/';
        if (Config::has('config.assetsURL'))
        {
            $assetsURL = Config::get('config.assetsURL');
        }

        if (self::urlExists($assetsURL . 'images/'))
        {
            foreach(self::$imageAssets as $imageName => $imageURL)
            {
                if (self::urlExists($assetsURL . $imageURL))
                {
                    self::$imageAssets[$imageName] = $assetsURL . $imageURL;
                }
            }
        }

        self::$initialized = true;
    }

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