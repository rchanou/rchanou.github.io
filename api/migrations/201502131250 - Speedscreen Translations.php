<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

$translationsSplitByCulture = array(
    "en-US" => array(
        "str_loadingSpeedScreen" => "Loading Speed Screen...",
        "str_connectingToServer" => "Connecting to server...",
        "str_gettingSettings" => "Getting settings...",
        "str_attachingAPI" => "Attaching API...",
        "str_fetchingChannelConfig" => "Fetching Channel configuration...",
        "str_channelHasNoSlides" => "Channel has no line-up available!",
        "str_noValidChannelData" => "No valid Channel data received!",
        "str_loadingChannel" => "Loading Channel...",
        "str_unableToGetChannels" => "Unable to get the channel configuration!",
        "str_unableToConnect" => "Unable to connect to server!",
        "str_speedScreenOffline" => "Speed Screen Offline",
        "str_restartingIn" => "Restarting in",
        "str_noValidSlidesToShow" => "No valid slides to show!"
    )
);

$translations = array(); //Flattening and formatting array for processing
foreach($translationsSplitByCulture as $culture => $translationsForCulture)
{
    foreach($translationsForCulture as $key => $translation)
    {
        $translations[] = array(
            "namespace" => "Speedscreen",
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
	$Namespace = 'Speedscreen';
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
		echo "Inserting " . $Name . "<br/>";
	} else {
		echo "Did not create " . $Name . " (" . $Culture . ") - (already exists)<br/>";
	}

}

// Confirm success
die('Successfully imported Speedscreen translations.');