<?php
/*
 * This migration copies OB settings over to mobile.
 * These include which fields are shown and required during user registration.
 *
 * If OB settings do not exist, defaults are used instead.
 *
 */
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Importing OB Settings to Mobile...<p>";

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$stmt = $conn->prepare("INSERT INTO ControlPanel (TerminalName, SettingName, DataType, DefaultSetting, SettingValue, Description, Fixed, CreatedDate) VALUES (:TerminalName, :SettingName, :DataType, :DefaultSetting, :SettingValue, :Description, :Fixed, GETDATE())");

//Main function used to insert a mobile setting if it does not yet exist
function insertMobileAppSetting($SettingName, $SettingValue, $DataType = null, $NewName = null, $TerminalName = 'MobileApp')
{
    global $conn, $stmt;

    $description = '';

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
        echo "Inserting " . $SettingName . " as " . $NewName . " with value " . htmlspecialchars($SettingValue) . "<br/>";
    } else {
        echo "Did not create " . $NewName . " (already exists)<br/>";
    }
}

$newMobileSettingNamesWithDefaults = array(
    'emailShown' => true,
    'emailRequired' => true,
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
);

//Fetch all existing booking settings
$existingBookingSettingsSql = $conn->prepare("SELECT * FROM ControlPanel WHERE TerminalName = 'Booking'");
$existingBookingSettings = array();
$existingBookingSettingsFormatted = array();
try
{
    $existingBookingSettingsSql->execute();
    $existingBookingSettings = $existingBookingSettingsSql->fetchAll();
}
catch(Exception $ex)
{

}

//Format the settings
foreach($existingBookingSettings as $currentSetting)
{
    $existingBookingSettingsFormatted[$currentSetting['SettingName']] = $currentSetting;
}

//Copy over any relevant and existing booking settings to mobile, defaulting to a fallback when required
if(!isset($existingBookingSettings) || count($existingBookingSettings) === 0)
{
    echo "There were no booking settings found. Using defaults for mobile.<p/>";

    foreach($newMobileSettingNamesWithDefaults as $settingName => $settingValue)
    {
        insertMobileAppSetting($settingName, $settingValue, 'bit');
    }
}
else
{
    echo "Booking settings found. Copying over for mobile.<p/>";

    foreach($newMobileSettingNamesWithDefaults as $settingName => $settingValue)
    {
        insertMobileAppSetting($settingName, $existingBookingSettingsFormatted[$settingName]['SettingValue'], 'bit');
    }
}

echo "<p/>Done.";