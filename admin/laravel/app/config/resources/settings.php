<?php

//TODO: Clear and concise documentation

//TODO: This is a copy/paste from the booking project. Need to personalize.
class Settings
{
    private static $defaultSettings;
    private static $currentSettings;

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultSettings()
    {
        self::initialize();
        return self::$defaultSettings;
    }

    public static function getSettings()
    {
        self::initialize();
        return self::$currentSettings;
    }

    private static function initialize()
    {
        if (self::$initialized) return;

        self::$defaultSettings = array(
            'emailShown' => true,
            'emailRequired' => true,
            'passwordShown' => true,
            'passwordRequired' => true,
            'consentToMailShown' => true,
            'companyShown' => true,
            'companyRequired' => false,
            'firstNameShown' => true,
            'firstNameRequired' => true,
            'lastNameShown' => true,
            'lastNameRequired' => true,
            'racerNameShown' => true,
            'racerNameRequired' => false,
            'birthDateShown' => true,
            'birthDateRequired' => true,
            'genderShown' => true,
            'genderRequired' => true,
            'whereDidYouHearAboutUsShown' => true,
            'whereDidYouHearAboutUsRequired' => false,
            'addressShown' => true,
            'addressRequired' => false,
            'cityShown' => true,
            'cityRequired' => false,
            'stateShown' => true,
            'stateRequired' => false,
            'zipShown' => true,
            'zipRequired' => false,
            'countryShown' => true,
            'countryRequired' => false,
            'cellShown' => true,
            'cellRequired' => false,
            'licenseNumberShown' => true,
            'licenseNumberRequired' => false,
            'custom1Shown' => true,
            'custom1Required' => false,
            'custom2Shown' => true,
            'custom2Required' => false,
            'custom3Shown' => true,
            'custom3Required' => false,
            'custom4Shown' => true,
            'custom4Required' => false,
            'forceFacebookRegistration' => false, //TODO: Implement
            'registrationEnabled' => true,
            'enableFacebook' => true
        );

        //TODO: API call to fetch and merge current settings from Club Speed (they're coming from a new admin panel)
        self::$currentSettings = self::$defaultSettings;

        self::$initialized = true;
    }
}
