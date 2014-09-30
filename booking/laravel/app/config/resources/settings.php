<?php

/**
 * Class Settings
 *
 * This class lists every default setting for the website, and overwrites them by pulling current settings via
 * Club Speed.
 */
class Settings
{
    private static $defaultSettings;
    private static $currentSettings;

    private function __construct() { }
    private static $initialized = false;

    //Returns the default settings for this website
    public static function getDefaultSettings()
    {
        self::initialize();
        return self::$defaultSettings;
    }

    //Returns the current settings for this website
    public static function getSettings($reset = false)
    {
        if ($reset)
        {
            self::$initialized = false;
        }

        self::initialize();

        return self::$currentSettings;
    }

    //Determines the current settings for this website, overwriting defaults with what is acquired from Club Speed
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$defaultSettings = array( //TODO: Change the default away from SagePay
            'onlineBookingPaymentProcessorSettings' =>   json_decode('{"name": "SagePay_Direct",
                                                          "options": {
                                                            "vendor": "clubspeed3",
                                                            "simulatorMode": true
                                                            }
                                                          }'),
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
            'forceRegistrationIfAuthenticatingViaThirdParty' => false,
            'registrationEnabled' => true,
            'enableFacebook' => true
        );

        self::$currentSettings = self::$defaultSettings;

        $settings = CS_API::getSettings(); //Kiosk settings
        $bookingSettings = CS_API::getBookingSettings(); //Online booking settings
        if ($settings === null || $bookingSettings === null)
        {
            return Redirect::to('/disconnected');
        }

        self::$currentSettings = array_merge(self::$currentSettings,$bookingSettings);

        //Fetch the custom text labels and store them in the session - for whatever reason CS treats them as settings
        self::$currentSettings['CustomText1'] = isset($settings->settings->CustomText1->SettingValue) ? $settings->settings->CustomText1->SettingValue : 'CustomText1';
        self::$currentSettings['CustomText2'] = isset($settings->settings->CustomText2->SettingValue) ? $settings->settings->CustomText2->SettingValue : 'CustomText2';
        self::$currentSettings['CustomText3'] = isset($settings->settings->CustomText3->SettingValue) ? $settings->settings->CustomText3->SettingValue : 'CustomText3';
        self::$currentSettings['CustomText4'] = isset($settings->settings->CustomText4->SettingValue) ? $settings->settings->CustomText4->SettingValue : 'CustomText4';
        self::$currentSettings['CustomText1'] = self::$currentSettings['CustomText1'] == '' ? 'CustomText1' : self::$currentSettings['CustomText1'];
        self::$currentSettings['CustomText2'] = self::$currentSettings['CustomText2'] == '' ? 'CustomText2' : self::$currentSettings['CustomText2'];
        self::$currentSettings['CustomText3'] = self::$currentSettings['CustomText3'] == '' ? 'CustomText3' : self::$currentSettings['CustomText3'];
        self::$currentSettings['CustomText4'] = self::$currentSettings['CustomText4'] == '' ? 'CustomText4' : self::$currentSettings['CustomText4'];

        //Fetch the dropdown menu settings and store them in the session
        $dropdownOptions = array();
        $dropdownOptions['0'] = 'Please select an option below:';
        foreach($settings->settings->Sources->SettingValue as $currentSource)
        {
            $dropdownOptions[$currentSource->SourceID] = $currentSource->SourceName;
        }

        self::$currentSettings['dropdownOptions'] = $dropdownOptions;
        self::$initialized = true;
    }
}
