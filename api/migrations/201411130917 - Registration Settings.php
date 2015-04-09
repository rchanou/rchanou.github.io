<?php
/**
 * Create the settings (with defaults) for cs-registration if they do not already exist.
 */

$settings = array(
	'genderShown' => true,
	'genderRequired' => true,
    'statusChangesWhenRegistered' => '{"statusChanges": [1,0,0,0]}', //Change status 1 to 1, change status 2 to 0, etc...
    'statusChangesWhenRegisteredForEvent' => '{"statusChanges": [1,0,0,0]}', //Change status 1 to 1, change status 2 to 0, etc...
    'zipValidated' => false
	);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $conn->prepare("INSERT INTO ControlPanel (TerminalName, SettingName, DataType, DefaultSetting, SettingValue, Description, Fixed, CreatedDate) VALUES (:TerminalName, :SettingName, :DataType, :DefaultSetting, :SettingValue, '', :Fixed, GETDATE())");

foreach($settings as $SettingName => $SettingValue) {
	$TerminalName = 'Registration';
	$Fixed = false;
	$DefaultSetting = $SettingValue;
	$DataType = gettype($SettingValue) === 'boolean' ? 'bit' : 65535;
	
	$sth = $conn->prepare("SELECT * FROM dbo.ControlPanel WHERE TerminalName = :TerminalName AND SettingName = :SettingName");
	$sth->bindParam(':TerminalName', $TerminalName);
	$sth->bindParam(':SettingName', $SettingName);
	$sth->execute();
	$existingEntry = $sth->fetchAll();
	
	// If it doesn't exist, insert it
	if(count($existingEntry) === 0) {
        if ($SettingName == 'statusChangesWhenRegistered') //Try to grab any existing status to migrate to new system
        {
            $statement = $conn->prepare("SELECT * FROM Rules WHERE RuleID IN (2)"); //"Add Customer From Registration Terminal" is hard-coded to 2
            $statement->execute();
            $statusWhenRegistered = $statement->fetchAll();
            if (isset($statusWhenRegistered[0]))
            {
                echo '<em>Fetching existing "Add Customer From Registration Terminal" rule to migrate into new settings table...<br/></em>';
                $statusChanges = array('statusChanges' => array());
                $statusChanges['statusChanges'][] =  $statusWhenRegistered[0]['ChangeStatus1'];
                $statusChanges['statusChanges'][] =  $statusWhenRegistered[0]['ChangeStatus2'];
                $statusChanges['statusChanges'][] =  $statusWhenRegistered[0]['ChangeStatus3'];
                $statusChanges['statusChanges'][] =  $statusWhenRegistered[0]['ChangeStatus4'];
                $SettingValue = json_encode($statusChanges);
            }
        }
        else if ($SettingName == 'statusChangesWhenRegisteredForEvent') //Try to grab any existing status to migrate to new system
        {
            $statement = $conn->prepare("SELECT * FROM Rules WHERE RuleID IN (4)"); //"Add Event Customer From Online Registration" is hard-coded to 4
            $statement->execute();
            $statusWhenRegisteredForEvent = $statement->fetchAll();
            if (isset($statusWhenRegisteredForEvent[0]))
            {
                echo '<em>Fetching existing "Add Event Customer From Online Registration" rule to migrate into new settings table...<br/></em>';
                $statusChanges = array('statusChanges' => array());
                $statusChanges['statusChanges'][] =  $statusWhenRegisteredForEvent[0]['ChangeStatus1'];
                $statusChanges['statusChanges'][] =  $statusWhenRegisteredForEvent[0]['ChangeStatus2'];
                $statusChanges['statusChanges'][] =  $statusWhenRegisteredForEvent[0]['ChangeStatus3'];
                $statusChanges['statusChanges'][] =  $statusWhenRegisteredForEvent[0]['ChangeStatus4'];
                $SettingValue = json_encode($statusChanges);
            }
        }
		$stmt->bindParam(':TerminalName', $TerminalName);
		$stmt->bindParam(':SettingName', $SettingName);
		$stmt->bindParam(':SettingValue', $SettingValue);
		$stmt->bindParam(':DefaultSetting', $DefaultSetting);
		$stmt->bindParam(':DataType', $DataType);
		$stmt->bindParam(':Fixed', $Fixed);
		$stmt->execute();
		echo "Inserting " . $SettingName . "<br/>";
	} else {
		echo "Did not create " . $SettingName . " (already exists)<br/>";
	}

}

// Confirm success
die('Successfully imported cs-registration settings.');