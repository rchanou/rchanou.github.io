<?php

$translationsSplitByCulture = array(
    "en-US" => array(
        "str_disconnected" => "Disconnected!",
        "str_reconnecting" => "Reconnecting...",
        "str_today" => "Today",
        "str_thisWeek" => "This Week",
        "str_thisMonth" => "This Month",
        "str_loading" => "Loading...",
        "str_waitingForNextRace" => "Waiting for next race...",
        "str_waitingForFirstLap" => "Waiting for first lap...",
        "str_finalResults" => "Final Results",
        "str_position_big" => "POS",
        "str_name_big" => "NAME",
        "str_kart_big" => "KART",
        "str_bestLap_big" => "BEST LAP",
        "str_lastLap_big" => "LAST LAP",
        "str_gap_big" => "GAP",
        "str_laps_big" => "LAPS",
        "str_position_classic" => "Pos",
        "str_name_classic" => "Racer Name",
        "str_kart_classic" => "Kart",
        "str_bestLap_classic" => "Best Time",
        "str_avgLap_classic" => "Avg",
        "str_lastLap_classic" => "Last",
        "str_gap_classic" => "Gap",
        "str_laps_classic" => "Laps",
        "str_comingUpNext" => "Coming Up Next",
        "str_heatNumber" => "Heat Number",
        "str_startTime" => "Start Time",
        "str_gridLineup" => "Grid Lineup",
        "str_noRacersYet" => "No racers yet!",
        "str_racerName" => "Racer Name",
        "str_topTimes" => "Top Times",
        "str_noneYet" => "None yet!",
        "str_poweredBy" => "Powered By"
    )
);

$translations = array(); //Flattening and formatting array for processing
foreach($translationsSplitByCulture as $culture => $translationsForCulture)
{
    foreach($translationsForCulture as $key => $translation)
    {
        $translations[] = array(
            "namespace" => "Scoreboard",
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
	$Namespace = 'Scoreboard';
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
die('Successfully imported Scoreboard translations.');