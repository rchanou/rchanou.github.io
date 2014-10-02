<?php
/**
 * Create the settings (with defaults) for "Reset Password" email if they do
 * not already exist.
 */
$settings = array(
	'resetPasswordHTML' => <<<EOS
<h4>Reset Password Request</h4>
<p>A password reset for your {{business}} account has been requested for this email!</p>
<p>
	If you made this request, please click the following link
	or copy and paste the URL into your browser 
	in order to continue with the password reset.
</p>
<a href="{{url}}">{{url}}</a>
<br>
<p>If you did not request this password reset, you may safely ignore this email.</p>
EOS
	, 'resetPasswordTEXT' => <<<EOS
A password reset for your {{business}} account has been requested for this email!

If you made this request, please click the following link or copy and paste the URL into your browser in order to continue with the password reset.

{{url}}

If you did not request this password reset, you may safely ignore this email.
EOS
);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $conn->prepare("INSERT INTO ControlPanel (TerminalName, SettingName, DataType, DefaultSetting, SettingValue, Description, Fixed, CreatedDate) VALUES (:TerminalName, :SettingName, :DataType, :DefaultSetting, :SettingValue, '', :Fixed, GETDATE())");

foreach($settings as $SettingName => $SettingValue) {
	$TerminalName = 'Templates';
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
die('Successfully imported online booking settings.');