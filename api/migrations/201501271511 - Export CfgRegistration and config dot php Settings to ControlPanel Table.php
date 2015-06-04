<?php
/**
 * Export deprecated Registration settings from CfgRegistration and config.php to Registration namespace in ControlPanel table.
 *
 */

$path_to_registration_config_directory = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cs-registration' . DIRECTORY_SEPARATOR . 'laravel' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;

$settingNames = array('defaultCountry', 'emailText', 'showTextingWaiver', 'textingWaiver', 'minorSignatureWithParent');


// See if we need to rename config.php
$registration_config_file = $path_to_registration_config_directory . 'config.php';
$default_config_file = $path_to_registration_config_directory . 'config.orig.php';

if(!file_exists($registration_config_file)) {
  //die($path_to_registration_config_directory . 'config.php does not exist. Cannot write to DB. Create the config.php file first.');
} else if(filesize($registration_config_file) == 0) {
  // If it exists but is 0 bytes, we should delete it and copy over the orig
  echo 'Config file is 0 bytes -- removing and replacing with config.orig.php<br/>';
  unlink($registration_config_file);
  copy($path_to_registration_config_directory . 'config.orig.php', $registration_config_file);
}


// Get private key, config objects and SQL connections
require_once('../config.php');
if(!isset($privateKey)) {
  die('$privateKey is not set! Exiting!');
}

$config = include $registration_config_file;
$defaultConfig = include $default_config_file;

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$stmt = $conn->prepare("INSERT INTO ControlPanel (TerminalName, SettingName, DataType, DefaultSetting, SettingValue, Description, Fixed, CreatedDate) VALUES (:TerminalName, :SettingName, :DataType, :DefaultSetting, :SettingValue, :Description, :Fixed, GETDATE())");


// main function to be used to import registration settings
function insertRegistrationSetting($SettingName, $SettingValue, $DataType = null, $NewName = null, $TerminalName = 'Registration'){
  global $conn, $stmt;

  $settingDescriptions = array(
    'defaultCountry' => 'This is the country that is selected by default.',
    'emailText' => 'This message will appear under the e-mail checkbox in Step 2.',
    'showTextingWaiver' => 'If checked, this will show the texting waiver.',
    'textingWaiver' => 'This is the text of the texting waiver.',
    'cfgRegAllowMinorToSign' => 'If checked, the minor may sign. If not, just clicking "Accept" will successfully end the registration.',
    'CfgRegDisblEmlForMinr' => 'If checked, does not allow minors to enter an e-mail address.',
    'CfgRegUseMsign' => 'If checked, signatures are enabled.',
    'minorSignatureWithParent' => 'If checked, minors must sign along with a parent or guardian.'
  );

  if (isset($settingDescriptions[$SettingName])){
    $description = $settingDescriptions[$SettingName];
  } else {
    $description = '';
  }

  $Fixed = false;

  if ($DataType === null){
    $DataType = gettype($SettingValue) === 'boolean' ? 'bit' : 65535;
  }
  if ($DataType === 'bit'){
    $SettingValue = $SettingValue? 1: 0;
  }
  if ($NewName === null){
    $NewName = $SettingName;
  }

  $sth = $conn->prepare("SELECT * FROM dbo.ControlPanel WHERE TerminalName = :TerminalName AND SettingName = :SettingName");
  $sth->bindParam(':TerminalName', $TerminalName);
  $sth->bindParam(':SettingName', $NewName);
  $sth->execute();
  $existingEntry = $sth->fetchAll();

  // If it doesn't exist, insert it
  if(count($existingEntry) === 0) {
    $stmt->bindParam(':TerminalName', $TerminalName);
    $stmt->bindParam(':SettingName', $SettingName);
    $stmt->bindParam(':SettingValue', $SettingValue);
    $stmt->bindParam(':DefaultSetting', $SettingValue);
    $stmt->bindParam(':Description', $description);
    $stmt->bindParam(':DataType', $DataType);
    $stmt->bindParam(':Fixed', $Fixed);
    $stmt->execute();
    echo "Inserting " . $SettingName . " as " . $NewName . "<br/>";
  } else {
    echo "Did not create " . $NewName . " (already exists)<br/>";
  }
}


// add config.php and/or config.orig.php settings to ControlPanel
foreach($settingNames as $SettingName) {
  if (isset($config[$SettingName])){
    $SettingValue = $config[$SettingName];
    insertRegistrationSetting($SettingName, $SettingValue);
  } else if (isset($defaultConfig[$SettingName])){
    $SettingValue = $defaultConfig[$SettingName];
    insertRegistrationSetting($SettingName, $SettingValue);
  } else {
    insertRegistrationSetting($SettingName, "");
  }
}


// copy CfgRegistration settings to ControlPanel
$cfgSth = $conn->prepare("SELECT * FROM dbo.CfgRegistration");

try {
  $cfgSth->execute();
  $cfgEntry = $cfgSth->fetchAll();
} catch(Exception $ex){
}

$cfgRegSettingNames = array(
  'cfgRegAllowMinorToSign' => '0',
  'CfgRegDisblEmlForMinr' => '1',
  'CfgRegUseMsign' => '1',
  'CfgRegAddShow' => '1',
  'CfgRegAddReq' => '0',
  'CfgRegCityShow' => '1',
  'CfgRegCityReq' => '0',
  'CfgRegStateShow' => '1',
  'CfgRegStateReq' => '0',
  'CfgRegZipShow' => '1',
  'CfgRegZipReq' => '0',
  'CfgRegCntryShow' => '1',
  'CfgRegCntryReq' => '0',
  'CfgRegRcrNameShow' => '1',
  'CfgRegRcrNameReq' => '1',
  'CfgRegSrcShow' => '1',
  'CfgRegSrcReq' => '0',
  'cfgRegCustTxt1Show' => '0',
  'cfgRegCustTxt1req' => '0',
  'cfgRegCustTxt2Show' => '0',
  'cfgRegCustTxt2req' => '0',
  'cfgRegCustTxt3Show' => '0',
  'cfgRegCustTxt3req' => '0',
  'cfgRegCustTxt4Show' => '0',
  'cfgRegCustTxt4req' => '0',
  'CfgRegDrvrLicShow' => '0',
  'CfgRegDrvrLicReq' => '0',
  'CfgRegPhoneShow' => '1',
  'CfgRegPhoneReq' => '0',
  'CfgRegEmailShow' => '1',
  'CfgRegEmailReq' => '0',
  'cfgRegShowBeenHereBefr' => '0',
  'CfgRegValidateGrp' => '0'
);

if(!isset($cfgEntry) || count($cfgEntry) === 0){
  echo "CfgRegistration table or settings not found. Inserting default entries.<br/>";

  foreach($cfgRegSettingNames as $settingName => $settingValue){
    insertRegistrationSetting($settingName, $settingValue, 'bit');
  }
} else {
  foreach($cfgRegSettingNames as $settingName => $settingValue){
    insertRegistrationSetting($settingName, $cfgEntry[0][$settingName], 'bit');
  }
}


// Add any missing Registration 1 and MainEngine registration settings
insertRegistrationSetting('enableWaiverStep', '1', 'bit', null, 'Registration1');
insertRegistrationSetting('FacebookPageURL', '', '65535', null, 'MainEngine');
insertRegistrationSetting('Reg_EnableFacebook', '1', 'bit', null, 'MainEngine');
insertRegistrationSetting('Reg_CaptureProfilePic', '0', 'bit', null, 'MainEngine');
insertRegistrationSetting('AgeNeedParentWaiver', '18', 'numericp', null, 'MainEngine');
insertRegistrationSetting('AgeAllowOnlineReg', '16', 'numericp', null, 'MainEngine');


// Confirm success
die('Successfully exported Registration settings from config.php file and/or CfgRegistration.');
