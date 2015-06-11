<?php

require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$translationsSplitByCulture = array(
    "en-US" => array(
        'str_welcomeMessage' => 'Welcome to the Official {Track_Name} Mobile App',
        'str_newAccount' => 'Sign Up',
        'str_checkIn' => 'Log In',
        'str_checkInFinal' => 'Sign In',
        'str_facebook' => 'Log in with Facebook',
        'str_skipLogin' => 'Skip Log in',
        'str_accountExists' => 'Already have an account?',
        'str_email' => 'Email',
        'str_password' => 'Password',
        'str_forgotPassword' => 'Forgot Password?',
        'str_Visits' => 'Visits',
        'str_Races' => 'Races',
        'str_ProSkill' => 'ProSkill',
        'str_CardID' => 'CardID',
        'str_emailText' => 'Please email me my results and special offers.',
        'str_termsAndConditions' => 'Please contact our facility for our latest Terms & Conditions.'
    )
);

$translations = array(); //Flattening and formatting array for processing
foreach($translationsSplitByCulture as $culture => $translationsForCulture)
{
    foreach($translationsForCulture as $key => $translation)
    {
        $translations[] = array(
            "namespace" => "MobileApp",
            "name" => $key,
            "culture" => $culture,
            "defaultValue" => $translation,
            "value" => $translation,
            "description" => null
        );
    }
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $conn->prepare("INSERT INTO Translations (Namespace, Name, Culture, DefaultValue, Value, Description, Created) VALUES (:Namespace, :Name, :Culture, :DefaultValue, :Value, :Description, GETDATE())");

foreach($translations as $translation) {
	$Namespace = 'MobileApp';
    $Name = $translation['name'];
    $Culture = $translation['culture'];
    $DefaultValue = $translation['defaultValue'];
    $Value = $translation['value'];
    $Description = $translation['description'];

	$sth = $conn->prepare("SELECT * FROM dbo.Translations WHERE Namespace = :Namespace AND Name = :Name AND Culture = :Culture");
	$sth->bindParam(':Namespace', $Namespace);
	$sth->bindParam(':Name', $Name);
    $sth->bindParam(':Culture', $Culture);
	$sth->execute();
	$existingEntry = $sth->fetchAll();
	
	// If it doesn't exist, insert it
	if(count($existingEntry) === 0) {
		$stmt->bindParam(':Namespace', $Namespace);
		$stmt->bindParam(':Name', $Name);
		$stmt->bindParam(':Culture', $Culture);
		$stmt->bindParam(':DefaultValue', $DefaultValue);
		$stmt->bindParam(':Value', $Value);
		$stmt->bindParam(':Description', $Description);
		$stmt->execute();
		echo "Inserting " . $Name . " (" . $Culture . ")<br/>";
	} else {
        echo "Did not create " . $Name . " (" . $Culture . ") - (already exists)<br/>";
	}

}

$settings = array(
    array(
        'TerminalName'    => 'MobileApp',
        'SettingName'  => 'currentCulture',
        'DataType'     => 'String',
        'DefaultSetting' => "en-US",
        'SettingValue' => "en-US",
        'Description'  => 'The current culture for the Mobile App',
        'Fixed'     => false
    )
);

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$stmt = $conn->prepare("INSERT INTO ControlPanel (TerminalName, SettingName, DataType, DefaultSetting, SettingValue, Description, Fixed, CreatedDate) VALUES (:TerminalName, :SettingName, :DataType, :DefaultSetting, :SettingValue, :Description, :Fixed, GETDATE())");

//Main function used to insert a mobile setting if it does not yet exist
function insertSetting($SettingName, $SettingValue, $DataType = null, $NewName = null, $TerminalName = 'MobileApp')
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

foreach($settings as $setting) {
    insertSetting($setting['SettingName'],$setting['SettingValue']);
}

echo '<p/>';

// Confirm success
die('Successfully imported Mobile translations and settings.');