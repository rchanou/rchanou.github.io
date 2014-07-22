<?php

class Settings
{
    public $restler;
		
		/**
     * Whitelist of groups/settings. Set setting (or group) to "true" if it is whitelisted.
     * By default, nothing is allowed.
     */
    private $isAllowedWhitelist = array(
      'Registration1' => array('RacerNameShow' => true),
      'kiosk' => true
    );
		
		private $settingsNotExistingInOlderVersions = array(
							array('SettingName' => 'CfgRegType', 'TerminalName' => 'kiosk', 'SettingValue' => 0, 'DataType' => 'int'),
							array('SettingName' => 'CfgRegRcrNameReq', 'TerminalName' => 'kiosk', 'SettingValue' => "true", 'DataType' => 'bit'), // TODO Should come from MainEngine > ShowRacerNameInRegistration
							array('SettingName' => 'CfgRegWlcmeTxt', 'TerminalName' => 'kiosk', 'SettingValue' => "", 'DataType' => '512'),
							array('SettingName' => 'CfgRegDisblEmlForMinr', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt1req', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt1Show', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt2req', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt2Show', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt3req', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt3Show', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt4req', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegCustTxt4Show', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegAllowMinorToSign', 'TerminalName' => 'kiosk', 'SettingValue' => "true", 'DataType' => 'bit'),
							array('SettingName' => 'cfgRegShowBeenHereBefr', 'TerminalName' => 'kiosk', 'SettingValue' => "false", 'DataType' => 'bit'),
							);
		private $oldToNew = array(
							'AcceptButtonPosition' => null,
							'AddressRequired' => 'CfgRegAddReq',
							'AddressShow' => 'CfgRegAddShow',
							'CityRequired' => 'CfgRegCityReq',
							'CityShow' => 'CfgRegCityShow',
							'CountryRequired' => 'CfgRegCntryReq',
							'CountryShow' => 'CfgRegCntryShow',
							'DriverLicenseRequired' => 'CfgRegDrvrLicReq',
							'DriverLicenseShow' => 'CfgRegDrvrLicShow',
							'EmailAddressShowStar' => 'CfgRegEmailShow',
							'EmailCheckBoxShow' => 'CfgRegEmailReq',
							'EmergencyContactShow' => null, // TODO How was this implemented in DB?
							'EmergencyPhoneShow' => null, // TODO How was this implemented in DB?
							'EmergencyRequired' => null, // TODO How was this implemented in DB?
							'GuarianInfoShow' => null, // TODO How is this populated now?
							'HotelRequired' => 'CfgRegHotelReq',
							'HotelShow' => 'CfgRegHotelShow',
							'Instruction' => 'CfgRegWaiverTrmsInstrcns',
							'IsWaiver2' => null, // TODO How is this populated now?
							'MySpaceShow' => null, // TODO Can be removed?
							'PassportNumberShow' => null, // TODO Can be removed?
							'PhoneRequired' => 'CfgRegPhoneReq',
							'PhoneShow' => 'CfgRegPhoneShow',
							'PreprintWaiver' => null, // TODO Can be removed, duplicated below
							'PrintWaiver' => 'CfgRegPrntWaiver',
							'RacerNameShow' => 'CfgRegRcrNameShow',
							'SourceRequired' => 'CfgRegSrcReq',
							'SourceShow' => 'CfgRegSrcShow',
							'StateRequired' => 'CfgRegStateReq',
							'StateShow' => 'CfgRegStateShow',
							'URLSecondLanguage' => null, // TODO Can be removed?
							'UseEsign' => 'CfgRegUseEsign',
							'UseMsign' => 'CfgRegUseMsign',
							'ValidateGroup' => 'CfgRegValidateGrp',
							'WaiverPrinterName' => 'CfgRegWaiverPrntrName',
							'WaiverPrinterNameUnderAged' => null, // TODO How was this implemented?
							'ZipRequired' => 'CfgRegZipReq',
							'ZipShow' => 'CfgRegZipShow',
							);

    protected function index($desiredData, $sub = null) {
				
				// Put settings into an array (if they are not already)
				if(isset($_GET['setting']) && !is_array($_GET['setting'])) $_GET['setting'] = array($_GET['setting']);
				
				$requestContainsPrivateSetting = false;
				
				// Check requested keys against the whitelist to see if we are looking for private settings
				if(isset($_GET['setting'])) {
						foreach(@$_GET['setting'] as $settingName) {
							if(@$this->isAllowedWhitelist[@$_GET['group']][$settingName] !== true) $requestContainsPrivateSetting = true;
						}
				} else {
						if(@$this->isAllowedWhitelist[@$_GET['group']] !== true) $requestContainsPrivateSetting = true;
				}
				
				// Require private key if we are looking for private settings
				if($requestContainsPrivateSetting && $_REQUEST['key'] != $GLOBALS['privateKey'])
					throw new RestException(412,'Not authorized');
				
				switch($desiredData) {
					case 'get':
						return $this->getSettings(@$_GET['group'], @$_GET['setting']);
						break;
					case 'getImages':
						return $this->getImages(@$_GET['app']);
						break;
        }
    }

		protected function getImages($app)
		{
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
						
						$rows = array();
						foreach($config[0] as $col => $val) {
							$dataType = ($val == "0" || $val == "1") ? 'bit' : '512';
							$rows[] = array('TerminalName' => 'kiosk', 'SettingName' => $col, 'SettingValue' => $val, 'DataType' => $dataType);
						}
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

						$mainEngineSettings = array('MainEngine' => array('AgeNeedParentWaiver','Reg_EnableFacebook','AllowDuplicateEmail','BusinessName'));

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

                        // Add in dropdown "How did you find us?" sources
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
						$rows[$key]['SettingValue'] = empty($rows[$key]['SettingValue']) || $rows[$key]['SettingValue'] == '0' || $rows[$key]['SettingValue'] == 'false' ? false : true;
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