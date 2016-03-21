<?php
/**
 * Create the settings (with defaults) for Online Booking if they do
 * not already exist.
 */
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

$settings = array(
	'onlineBookingPaymentProcessorSettings' => '{"name": "Dummy","options": {}}',
  'onlineBookingPaymentProcessorSavedSettings' => '{"Dummy": {"name": "Dummy","options": {}}}',
	'defaultPaymentCountry' => '',
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
	'enableFacebook' => true,
	'bookingAvailabilityWindowBeginningInSeconds' => 30*60,
	'bookingAvailabilityWindowEndingInSeconds' => 90*24*60*60,
	'reservationTimeout' => 30*60,
	'autoAddRacerToHeat' => true,
    'showTermsAndConditions' => true,
    'sendReceiptCopyTo' => '',
    'showLanguageDropdown' => false,
    'dateDisplayFormat' => 'Y-m-d', //http://php.net/manual/en/function.date.php
    'timeDisplayFormat' => 'H:i', //http://php.net/manual/en/function.date.php
    'currency' => 'USD', //http://www.xe.com/iso4217.php
    'numberFormattingLocale' => 'en_US', //http://www.oracle.com/technetwork/java/javase/javase7locales-334809.html
    'maxRacersForDropdown' => 50,
    'currentCulture' => 'en-US',
	'giftCardSalesEnabled' => false,
	'giftCardsAvailableForOnlineSale' => '{"giftCardProductIDs": []}',
	'brokerFieldEnabled' => false,
	'brokerSourceInURLEnabled' => false
	);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stmt = $conn->prepare("INSERT INTO ControlPanel (TerminalName, SettingName, DataType, DefaultSetting, SettingValue, Description, Fixed, CreatedDate) VALUES (:TerminalName, :SettingName, :DataType, :DefaultSetting, :SettingValue, '', :Fixed, GETDATE())");

foreach($settings as $SettingName => $SettingValue) {
	$TerminalName = 'Booking';
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