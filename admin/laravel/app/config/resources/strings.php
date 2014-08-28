<?php

//TODO: Clear and concise documentation

//TODO: This is a copy/paste from the booking project. Need to personalize.
class Strings
{
    private static $defaultStrings;
    private static $currentStrings;
    private static $errorCodes;

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultStrings()
    {
        self::initialize();
        return self::$defaultStrings;
    }

    public static function getStrings()
    {
        self::initialize();
        return self::$currentStrings;
    }

    public static function getErrorCodes()
    {
        self::initialize();
        return self::$errorCodes;
    }

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
