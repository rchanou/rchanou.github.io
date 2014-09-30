<?php

/**
 * Class Strings
 *
 * This class is used through the website to acquire strings for use.
 * It determines defaults, and pulls translations from Club Speed.
 */
class Strings
{
    private static $defaultStrings;
    private static $currentStrings;
    private static $errorCodes;

    private function __construct() { }
    private static $initialized = false;

    //Gets the default strings
    public static function getDefaultStrings()
    {
        self::initialize();
        return self::$defaultStrings;
    }

    //Gets the current strings
    public static function getStrings()
    {
        self::initialize();
        return self::$currentStrings;
    }

    //Gets error code strings
    public static function getErrorCodes()
    {
        self::initialize();
        return self::$errorCodes;
    }

    //Sets default strings, and overwrites them with strings from Club Speed
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$defaultStrings = array(
            'Custom1' => 'Custom1',
            'Custom2' => 'Custom2',
            'Custom3' => 'Custom3',
            'Custom4' => 'Custom4'
        );

        self::$errorCodes = array(
            'emailAlreadyExists' => 'ERR001'
        );

        //TODO: API call to fetch and merge current strings from Club Speed (they're coming from a new admin panel)
        //TODO: Implement localization support

        self::$currentStrings = self::$defaultStrings;

        self::$initialized = true;
    }
}
