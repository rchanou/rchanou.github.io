<?php

use ClubSpeed\Enums\Enums as Enums;

class Settings extends BaseApi
{
    // public $restler;

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\SettingsMapper();
        $this->interface = $this->logic->settings;

        // mobile app (client code) needs access to settings -- making public
        $this->access['get']    = Enums::API_PUBLIC_ACCESS;
        $this->access['match']  = Enums::API_PUBLIC_ACCESS;
        $this->access['filter'] = Enums::API_PUBLIC_ACCESS;
        $this->access['all']    = Enums::API_PUBLIC_ACCESS;
    }

    /**
     * Whitelist of groups/settings. Set setting (or group) to "true" if it is whitelisted.
     * By default, nothing is allowed.
     */
    private $isAllowedWhitelist = array(
        'Registration1' => array(
            'RacerNameShow' => true
        ),
        'kiosk' => true,
        'ScotKart' => true,
        'MobileApp' => true,
        'Booking' => array(
            'giftCardSalesEnabled' => true
        )
    );

    private $settingsNotExistingInOlderVersions = array(
        array('SettingName' => 'CfgRegType',                'TerminalName' => 'kiosk', 'SettingValue' => 0,         'DataType' => 'int'),
        array('SettingName' => 'CfgRegRcrNameReq',          'TerminalName' => 'kiosk', 'SettingValue' => "true",    'DataType' => 'bit'), // TODO Should come from MainEngine > ShowRacerNameInRegistration
        array('SettingName' => 'CfgRegWlcmeTxt',            'TerminalName' => 'kiosk', 'SettingValue' => "",        'DataType' => '512'),
        array('SettingName' => 'CfgRegDisblEmlForMinr',     'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt1req',         'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt1Show',        'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt2req',         'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt2Show',        'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt3req',         'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt3Show',        'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt4req',         'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegCustTxt4Show',        'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
        array('SettingName' => 'cfgRegAllowMinorToSign',    'TerminalName' => 'kiosk', 'SettingValue' => "true",    'DataType' => 'bit'),
        array('SettingName' => 'cfgRegShowBeenHereBefr',    'TerminalName' => 'kiosk', 'SettingValue' => "false",   'DataType' => 'bit'),
    );

    private $oldToNew = array(
        'AcceptButtonPosition'          => null,
        'AddressRequired'               => 'CfgRegAddReq',
        'AddressShow'                   => 'CfgRegAddShow',
        'CityRequired'                  => 'CfgRegCityReq',
        'CityShow'                      => 'CfgRegCityShow',
        'CountryRequired'               => 'CfgRegCntryReq',
        'CountryShow'                   => 'CfgRegCntryShow',
        'DriverLicenseRequired'         => 'CfgRegDrvrLicReq',
        'DriverLicenseShow'             => 'CfgRegDrvrLicShow',
        'EmailAddressShowStar'          => 'CfgRegEmailShow',
        'EmailCheckBoxShow'             => 'CfgRegEmailReq',
        'EmergencyContactShow'          => null, // TODO How was this implemented in DB?
        'EmergencyPhoneShow'            => null, // TODO How was this implemented in DB?
        'EmergencyRequired'             => null, // TODO How was this implemented in DB?
        'GuarianInfoShow'               => null, // TODO How is this populated now?
        'HotelRequired'                 => 'CfgRegHotelReq',
        'HotelShow'                     => 'CfgRegHotelShow',
        'Instruction'                   => 'CfgRegWaiverTrmsInstrcns',
        'IsWaiver2'                     => null, // TODO How is this populated now?
        'MySpaceShow'                   => null, // TODO Can be removed?
        'PassportNumberShow'            => null, // TODO Can be removed?
        'PhoneRequired'                 => 'CfgRegPhoneReq',
        'PhoneShow'                     => 'CfgRegPhoneShow',
        'PreprintWaiver'                => null, // TODO Can be removed, duplicated below
        'PrintWaiver'                   => 'CfgRegPrntWaiver',
        'RacerNameShow'                 => 'CfgRegRcrNameShow',
        'SourceRequired'                => 'CfgRegSrcReq',
        'SourceShow'                    => 'CfgRegSrcShow',
        'StateRequired'                 => 'CfgRegStateReq',
        'StateShow'                     => 'CfgRegStateShow',
        'URLSecondLanguage'             => null, // TODO Can be removed?
        'UseEsign'                      => 'CfgRegUseEsign',
        'UseMsign'                      => 'CfgRegUseMsign',
        'ValidateGroup'                 => 'CfgRegValidateGrp',
        'WaiverPrinterName'             => 'CfgRegWaiverPrntrName',
        'WaiverPrinterNameUnderAged'    => null, // TODO How was this implemented?
        'ZipRequired'                   => 'CfgRegZipReq',
        'ZipShow'                       => 'CfgRegZipShow',
    );

    public function index($desiredData, $sub = null) {

        $requestContainsPrivateSetting = false;

        // Handling these cases:
        //   - Want a group: /settings/get.json?key=cs-dev&group=MainEngine
        //   - Want a single setting from a group /settings/get.json?key=cs-dev&group=MainEngine&setting=currentCulture
        //   - Want multiple settings from a group: /settings/get.json?key=cs-dev&group=MainEngine&setting[]=currentCulture&setting[]=AcceptExternalPayment

        // Error cases to handle:
        //   - Group is missing
        //   - Enforce private key if *ANY* of the settings we want are private (all or nothing)

        // Handle missing "group" parameter
        if(empty($_GET['group'])) throw new RestException(400, "Bad Request: Missing 'group' parameter");

        // Put settings into an array (if they are not already)
        if(isset($_GET['setting']) && !is_array($_GET['setting'])) $_GET['setting'] = array($_GET['setting']);

        //Enforce whitelist

        //If the request is for the entire group, but entire group isn't whitelisted, private access is needed
        $requestIsForEntireGroup = !isset($_GET['setting']);
        $groupIsFullyWhiteListed = (isset($this->isAllowedWhitelist[$_GET['group']]) && $this->isAllowedWhitelist[$_GET['group']] === true);
        if ($requestIsForEntireGroup && !$groupIsFullyWhiteListed) { $requestContainsPrivateSetting = true; }

        //If the request is for specific settings, but one or more are not represented in the whitelist, private access is needed
        $requestIsForOneOrMoreSpecificSettings = !$requestIsForEntireGroup;
        if($requestIsForOneOrMoreSpecificSettings && !$groupIsFullyWhiteListed)
        {
            foreach($_GET['setting'] as $settingName) {
                $settingIsNotInWhiteList = ( !isset($this->isAllowedWhitelist[$_GET['group']][$settingName]) || $this->isAllowedWhitelist[$_GET['group']][$settingName] !== true);
                if ($settingIsNotInWhiteList) {
                    $requestContainsPrivateSetting = true;
                    break;
                }
            }
        }

        // Require private key if we are looking for private settings
        if($requestContainsPrivateSetting && !\ClubSpeed\Security\Authenticate::privateAccess()) throw new RestException(401, "Invalid authorization!");

        switch($desiredData) {
            case 'get':
                return $this->getSettings(@$_GET['group'], @$_GET['setting']);
                break;
            case 'getImages':
                return $this->getImages(@$_GET['app']);
                break;
        }
    }

    public function getImages($app)
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        //TODO: Default images are currently hard-coded. Will eventually be pulled from Club Speed.
        if ($app === "kiosk")
        {
            return array(
                'bg_image' => 'http://localhost/cs-assets/cs-registration/images/bg_default.jpg',
                'createAccount' => 'http://localhost/cs-assets/cs-registration/images/new_account.png',
                'createAccountFacebook' => 'http://localhost/cs-assets/cs-registration/images/facebook_connect.png',
                'venueLogo' => 'http://localhost/cs-assets/cs-registration/images/default_header.png',
                'poweredByClubSpeed' => 'http://localhost/cs-assets/cs-registration/images/clubspeed.png',
                'completeRegistration' => 'http://localhost/cs-assets/cs-registration/images/complete_registration.png'
            );
        }
        else
        {
            throw new RestException(412,'Image group ' . $app . ' is not recognized.');
        }

    }

    public function getSettings($group, $setting = null)
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        $output = array();
        if(!empty($setting)) { // Get specific settings
            $tsql_params = array(&$group);

            // Create placeholders in query
            $placeholders = array();
            foreach($setting as $key => $settingName) {
                $tsql_params[] = $settingName;
                $placeholders[] = '?';
            }

            $tsql = 'SELECT * FROM ControlPanel WHERE TerminalName = ? AND SettingName IN ' . '(' . implode(',', $placeholders) . ')';

            $rows = $this->run_query($tsql, $tsql_params);

        } else { // Get entire group

            $registrationVersion = $this->getRegistrationVersion(); // Flag so that we can handle registration differently.

            if($group == 'kiosk' && $registrationVersion == 'new') {
                // If the CfgRegistration table exists, select from there
                $tsql = "SELECT * FROM CfgRegistration WHERE CfgRegType = ?";
                $tsql_params = array('0');
                $config = $this->run_query($tsql, $tsql_params);

                $rowsConfig = array();
                foreach($config[0] as $col => $val) {
                    $dataType = ($val == "0" || $val == "1") ? 'bit' : '512';
                    $rowsConfig[] = array('TerminalName' => 'kiosk', 'SettingName' => $col, 'SettingValue' => $val, 'DataType' => $dataType);
                }

                // Get older "Registration1" Control Panel Settings
                // Create placeholders in query
                $placeholders = array();
                $setting = array('enableWaiverStep','PreprintWaiver','WaiverPrinterNameUnderAged');
                $tsql_params = array('Registration1');
                foreach($setting as $key => $settingName) {
                    $tsql_params[] = $settingName;
                    $placeholders[] = '?';
                }

                $tsql = "SELECT * FROM ControlPanel WHERE TerminalName = ? AND SettingName IN " . '(' . implode(',', $placeholders) . ')';
                $rowsRegistration1 = $this->run_query($tsql, $tsql_params);

                $rows = array_merge($rowsRegistration1, $rowsConfig);
            } elseif($group == 'kiosk' && $registrationVersion == 'old') {
                // Else, get from control panel as Registration1 and format to match CfgRegType format
                $tsql = "SELECT * FROM ControlPanel WHERE TerminalName = ?";
                $tsql_params = array('Registration1');
                $rows = $this->run_query($tsql, $tsql_params);

                // TODO Loop each row and remap array keys
                foreach($rows as $key => $values) {
                    $values['TerminalName'] = 'kiosk';
                    if(array_key_exists($values['SettingName'], $this->oldToNew)) {

                        // Pass through older settings (new kiosk does not account for these)
                        if($this->oldToNew[$values['SettingName']] == null) {
                            //unset($rows[$key]); // Uncomment to remove the older settings
                        } else { // Map the older setting to newer one
                            $rows[$key]['SettingName'] = $this->oldToNew[$values['SettingName']];
                        }

                    }
                }

                // Add settings that are not part of the older settings w/ defaults
                foreach($this->settingsNotExistingInOlderVersions as $key => $val) {
                    $rows[$val['SettingName']] = $val;
                }

            } else {
                // Get any other group
                $tsql = "SELECT * FROM ControlPanel WHERE TerminalName = ?";
                $tsql_params = array(&$group);
                $rows = $this->run_query($tsql, $tsql_params);
            }

            // Applies to all kiosks
            if($group == 'kiosk') {

                $mainEngineSettings = array('MainEngine' => array('CustomText1','CustomText2','CustomText3','CustomText4','AgeAllowOnlineReg','AgeNeedParentWaiver','Reg_EnableFacebook','AllowDuplicateEmail','BusinessName','FacebookPageURL','Reg_CaptureProfilePic'));

                foreach($mainEngineSettings as $group => $values) {
                    foreach($values as $currentValue)
                    {
                        $setting = $this->getSettings($group, array($currentValue));
                        if (array_key_exists('settings',$setting) && array_key_exists($currentValue,$setting['settings']))
                        {
                            $rows[] = $setting['settings'][$currentValue];
                        }
                    }
                }

                // Add in waivers (Club Speed is hardcoded to 1 = Adult, 2 = Kid)
                $tsql = "SELECT * FROM WaiverTemplates WHERE Waiver IN (1,2)";
                $tsql_params = array();
                $waivers = $this->run_query($tsql, $tsql_params);
                foreach($waivers as $waiver) {
                    $rows[] = array(
                        'TerminalName' => 'kiosk',
                        'SettingName' => "Waiver{$waiver['Waiver']}",
                        'Description' => $waiver['Description'],
                        'SettingValue' => $waiver['WaiverText'],
                        'DataType' => '1024000'
                    );
                }


                /**
                 * Add in dropdown for Events running today
                 */

                // Find CfgRegValidateGrp
                $CfgRegValidateGrp = false;
                foreach($rows as $row) {
                    if($row['SettingName'] == 'CfgRegValidateGrp') {
                        $CfgRegValidateGrp = filter_var($row['SettingValue'], FILTER_VALIDATE_BOOLEAN);
                    }
                }

                if($CfgRegValidateGrp) {
                    $tsql = "GetTodayEventsForRegistration";
                    $tsql_params = array();
                    $events = $this->run_query($tsql, $tsql_params);
                    $eventsById = array();

                    $eventsById[] = array('-1' => 'Walk-in');
                    foreach($events as $event) {
                        $eventsById[] = array($event['EventID'] => $event['EventDesc']);
                    }

                    $rows[] = array(
                        'TerminalName' => 'kiosk',
                        'SettingName' => "eventGroups",
                        'Description' => "Events available for registration today",
                        'SettingValue' => $eventsById,
                        'DataType' => '1024000'
                    );
                }

                /**
                 * Add in dropdown "How did you find us?" sources
                 */

                $tsql = "SELECT * FROM Sources";
                $tsql_params = array();
                $sources = $this->run_query($tsql, $tsql_params);
                $sourcesByLocationID = array();
                foreach($sources as $source) {
                    if ($source['Deleted'] == "0" && $source['Enabled'] == "1")
                    {
                        $sourcesByLocationID[$source['SourceName']] = $source;
                        if (!array_key_exists('LocationID',$source))
                        {
                            $source['LocationID'] = '1'; //If older Club Speeds do not have LocationID, default to 1
                        }
                    }
                }
                $rows[] = array(
                    'TerminalName' => 'kiosk',
                    'SettingName' => "Sources",
                    'Description' => "How Did You Hear About Us sources",
                    'SettingValue' => $sourcesByLocationID,
                    'DataType' => '1024000'
                );
            }

            // Handle decoders
            if ($group == "decoders")
            {
                // Add in decoders
                $tsql = "SELECT * FROM TimerControls";
                $tsql_params = array();
                $decoders = $this->run_query($tsql, $tsql_params);
                $decodersByLoopID = array();
                foreach($decoders as $decoder) {
                    $decodersByLoopID[$decoder['LoopID']] = $decoder;
                }
                $rows[] = array(
                    'TerminalName' => 'decoders',
                    'SettingName' => "Decoders",
                    'Description' => "Decoders from the TimerControls table",
                    'SettingValue' => $decodersByLoopID,
                    'DataType' => '1024000'
                );
            }
        }

        foreach($rows as $key => $val) {
            if($val['DataType'] == 'bit') {
                $rows[$key]['SettingValue'] = filter_var($rows[$key]['SettingValue'], FILTER_VALIDATE_BOOLEAN);
            }
            $output[$val['SettingName']] = $rows[$key];
        }

        return array('settings' => $output);
    }

    private function getRegistrationVersion() {
        $tsql = "IF OBJECT_ID (N'CfgRegistration', N'U') IS NOT NULL SELECT 'new' AS res ELSE SELECT 'old' AS res;";
        $tsql_params = array();
        $rows = $this->run_query($tsql, $tsql_params);
        return $rows[0]['res'];
    }

    private function run_query($tsql, $params = array(), $database = null) {
        $tsql_original = $tsql . ' ';

        // Setting default for older installs
        $GLOBALS['defaultDatabase'] = empty($GLOBALS['defaultDatabase']) ? 'ClubspeedV8' : $GLOBALS['defaultDatabase'];

        // Allow database to be overridden
        $database = empty($database) ? $GLOBALS['defaultDatabase'] : $database;

        // Connect
        try {
            $conn = new PDO("sqlsrv:server=(local) ; Database=" . $database, "", "");
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            // Prepare statement
            $stmt = $conn->prepare($tsql);

            // Execute statement
            $stmt->execute($params);

            // Put in array
            $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            die('Exception Message:'  . $e->getMessage()  . '<br/>(Line: '. $e->getLine() . ')' . '<br/>Passed query: ' . $tsql_original . '<br/>Parameters passed: ' . print_r($params,true));
        }

        return $output;
    }
}