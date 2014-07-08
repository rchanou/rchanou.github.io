<?php

/**
 * Class Settings
 *
 * This class holds the settings that are used throughout the registration website.
 * Default settings are included.
 *
 * Ultimately, a global session variable 'settings' is assigned to the desired settings array.
 * That array is then used to enforce custom settings throughout the website.
 */
class Settings {

    private static $defaultSettings;

    private function __construct() { }
    private static $initialized = false;

    public static function getDefaultSettings()
    {
        self::initialize();
        return self::$defaultSettings;
    }
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$defaultSettings = array(
            'Reg_EnableFacebook' => true,
            'showPicture' => true, //Assuming always required
            'showBirthDate' => true, //Always required
            'requireBirthDate' => true, //Always required
            'CfgRegPhoneShow' => true,
            'CfgRegPhoneReq' => false,
            'CfgRegSrcShow' => true, //How did you hear about us?
            'CfgRegSrcReq' => false,
            'showFirstName' => true, //Always required
            'requireFirstName' => true, //Always required
            'showLastName' => true, //Always required
            'requireLastName' => true, //Always required
            'CfgRegRcrNameShow' => true, //Always required
            'CfgRegRcrNameReq' => true, //Always required
            'CfgRegEmailShow' => true,
            'CfgRegEmailReq' => false,
            'dropdownOptions' => array('0' => '', '1' => 'Radio', '2' => 'Magazine', '3' => 'Billboard'),
            'AllowDuplicateEmail' => true,
            'Waiver1' => ''
            //TODO: Add and handle unique e-mail option.
        );

        /* //TODO: Analysis of current settings and how they fit into the current app
         {
           "CfgRegType":false, //TODO: Unknown. Unused.
           "CfgRegAddReq":true, //TODO: Address? Unused.
           "CfgRegAddShow":true, //TODO: Address? Unused.
           "CfgRegCityReq":true, //TODO: Unused.
           "CfgRegCityShow":true, //TODO: Unused.
           "CfgRegCntryReq":true, //TODO: Unused.
           "CfgRegCntryShow":true, //TODO: Unused.
           "CfgRegDrvrLicReq":false, //TODO: Unused.
           "CfgRegDrvrLicShow":false, //TODO: Unused.
           "CfgRegEmailShow":true, //TODO: USED!
           "CfgRegEmailReq":false, //TODO: USED!
           "CfgRegHotelReq":false, //TODO: Unused.
           "CfgRegHotelShow":false, //TODO: Unused.
           "CfgRegPhoneReq":true, //TODO: USED!
           "CfgRegPhoneShow":true, //TODO: USED!
           "CfgRegRcrNameShow":true, //TODO: USED!
           "CfgRegRcrNameReq":true, //TODO: USED!
           "CfgRegSrcReq":false, //TODO: How did you hear about us? USED!
           "CfgRegSrcShow":true, //TODO: How did you hear about us? USED!
           "CfgRegStateReq":false, //TODO: Unused.
           "CfgRegStateShow":false, //TODO: Unused.
           "CfgRegZipReq":true, //TODO: Unused.
           "CfgRegZipShow":true, //TODO: Unused.
           "CfgRegWaiverTrmsInstrcns":"Read Waiver and Accept Terms at the Bottom of the Page", //TODO: This isn't a setting! Someone cheated!
           "CfgRegPrntWaiver":true, //TODO: Unused.
           "CfgRegUseEsign":false, //TODO: ?
           "CfgRegUseMsign":false, //TODO: ?
           "CfgRegValidateGrp":true, //TODO: ?
           "CfgRegWaiverPrntrName":"", //TODO: Okay, I can consider this a setting.
           "CfgRegWlcmeTxt":"Welcome...", //TODO: This isn't a setting, you cheaters!
           "CfgRegDisblEmlForMinr":false, //TODO: Unused.
           "cfgRegCustTxt1req":false, //TODO: Unused. Should add?
           "cfgRegCustTxt1Show":false, //TODO: Unused. Should add?
           "cfgRegCustTxt2req":false, //TODO: Unused. Should add?
           "cfgRegCustTxt2Show":false, //TODO: Unused. Should add?
           "cfgRegCustTxt3req":false, //TODO: Unused. Should add?
           "cfgRegCustTxt3Show":false, //TODO: Unused. Should add?
           "cfgRegCustTxt4req":false, //TODO: Unused. Should add?
           "cfgRegCustTxt4Show":false, //TODO: Unused. Should add?
           "cfgRegAllowMinorToSign":false,
           "cfgRegShowBeenHereBefr":true //TODO: Unused. Need to implement?
         }
         */
        /*self::$defaultSettings = array(
            'Reg_EnableFacebook' => true,
            'showPicture' => true,
            'showBirthDate' => true,
            'requireBirthDate' => true,
            'CfgRegPhoneShow' => true,
            'CfgRegPhoneReq' => true,
            'CfgRegSrcShow' => true,
            'CfgRegSrcReq' => true,
            'showFirstName' => true,
            'requireFirstName' => true,
            'showLastName' => true,
            'requireLastName' => true,
            'CfgRegRcrNameShow' => true,
            'CfgRegRcrNameReq' => true,
            'CfgRegEmailShow' => true,
            'CfgRegEmailReq' => true
        );*/

        self::$initialized = true;
    }
} 