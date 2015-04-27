<?php
/**
 * Update the db schema for the new Speed Screen and import old Speed Screens
 */
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
require_once('../restler/restler.php'); //Super hacky...
require_once('../Channel.php'); //Super hacky...
require_once("../Version.php"); //Super hacky...


$channelAPI = new Channel(); //Restler's Channel API

$_REQUEST['debug'] = true;

//If 'overwrite=1' is in the URL, the import will overwrite/update any existing channels in the new SpeedScreenChannels table
$shouldOverwriteChannels = (isset($_GET['overwrite']) && $_GET['overwrite'] == 1) ? true : false;

$resourceDir = './resources/';
$files = scandir($resourceDir);
$date = '201502240906'; // only grab sql files with this exact date
$sql_files = array_filter($files, function($file) use ($date) {
    return ((strpos($file, $date) === 0) && (substr($file, -4) === '.sql'));
});

$noFailures = true;

foreach($sql_files as $sql_file) {
    $sql = file_get_contents($resourceDir . $sql_file);
    try {
        $db->exec($sql);
        echo $sql_file . ' executed successfully!';
        echo '<br>';
    }
    catch (Exception $e) {
        echo 'Unable to execute ' . $sql_file . '! ' . $e->getMessage();
        echo '<br>';
        //$noFailures = false; //Cannot trust 2003 servers to accurately report errors.
    }
}
echo 'All schema updates complete!<p/>';

if ($noFailures) //If SpeedScreenChannels exists, import old Speed Screens into the new format if they haven't been yet
{
    echo 'Checking if older Speed Screens need to be imported...<p/>';

    $result = $db->query('SELECT COUNT(*) as numOfChannels FROM SpeedScreenChannels');

    $numOfChannels = isset($result[0]['numOfChannels']) ? (int)$result[0]['numOfChannels'] : null;

    if ($numOfChannels === null)
    {
        echo 'Unable to determine the number of channels in SpeedScreenChannels. Aborting import.';
    }
    else if ($numOfChannels > 0 && !$shouldOverwriteChannels)
    {
        echo 'SpeedScreenChannels already has channels defined. Aborting import.';
    }
    else if ($numOfChannels === 0 || $shouldOverwriteChannels)
    {
        if ($numOfChannels === 0)
        {
            echo 'SpeedScreenChannels does not have any channels yet -- importing the old channels!<p/>';
        }
        else if ($shouldOverwriteChannels)
        {
            echo 'SpeedScreenChannels has channels but overwrite mode was set to TRUE. Wiping out all channels in SpeedScreenChannels!<p/>';
            $result = $db->exec('TRUNCATE TABLE SpeedScreenChannels');
        }

        //Determine which old Speed Screen channels exist
        $result = $db->query('SELECT TemplateID FROM ScreenTemplate');
        if (is_array($result))
        {
            if (count($result) > 0)
            {
                $channelIDs = array();
                foreach($result as $channel)
                {
                    $channelIDs[] = (int)$channel['TemplateID'];
                }

                $conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

                $stmt = $conn->prepare("INSERT INTO SpeedScreenChannels (ChannelNumber, ChannelData, Created) VALUES (:ChannelNumber, :ChannelData, GETDATE())");

                foreach($channelIDs as $currentChannelID) //For every old Speed Screen Channel
                {
                    echo 'Importing Channel ' . $currentChannelID . '... ';

                    //Fetch the channel
                    $_REQUEST['key'] = $privateKey; //Super hacky...
                    try {
                        $channelData = $channelAPI->get($currentChannelID);
                    }
                    catch(RestException $e)
                    {
                        echo "<strong>Channel was either not found or has been deleted. Skipped import.</strong><br/>";
                        continue;
                    }

                    //Convert the channel to the new format
                    $newChannelFormat = json_decode('{
                                                "name": "",
                                                "hash": "",
                                                "options": {},
                                                "timelines": {
                                                    "regular": {
                                                        "options": {},
                                                        "slides": []
                                                    },
                                                    "races": {
                                                        "options": {},
                                                        "slides": []
                                                    }
                                                }
                                              }');

                    $listOfTracksWithScoreboards = array(); //Used to prevent duplicate scoreboard slides at the same track
                    foreach($channelData["lineup"] as $currentSlide)
                    {
                        if (isset($currentSlide["options"]["backgroundUrl"]) && strpos($currentSlide["options"]["backgroundUrl"],"assets/cs-speedscreen/images/background_1080p.jpg") !== -1) //Check the given background image URL
                        {
                            $pathToImageAssets = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cs-speedscreen' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                            $defaultImageFile = $pathToImageAssets . 'background_1080p.jpg';

                            if(!file_exists($defaultImageFile)) //If the image doesn't exist, remove the backgroundUrl setting.
                            {
                                unset($currentSlide["options"]["backgroundUrl"]);
                            }
                        }
                        if ($currentSlide["type"] == "scoreboard")
                        {
                            $trackAlreadyInAScoreboardSlide = (in_array($currentSlide["options"]["trackId"],$listOfTracksWithScoreboards));
                            if (!$trackAlreadyInAScoreboardSlide)
                            {
                                $listOfTracksWithScoreboards[] = $currentSlide["options"]["trackId"];
                            }
                            else
                            {
                                continue; //Don't insert multiple scoreboards for the same track
                            }

                            if (isset($currentSlide['options']))
                            {
                                $defaultScoreboardSettings = array(
                                    "postRaceIdleTime" => 15000,
                                    "trackId" => 1,
                                    "theme" => "classic",
                                    "pollingInterval" => 1000,
                                    "headerEnabled" => 1,
                                    "showHeatNumber" => 1,
                                    "showHeatTime" => 0,
                                    "showHeaderTimer" => 0,
                                    "locale" => "en-US",
                                    "highlightFastestRacer" => 1,
                                    "fastestRacerColor" => "00FF00",
                                    "textLabelsColor" => "FFFFFF",
                                    "textDataColor" => "FFD700",
                                    "racersPerPage" => 10,
                                    "timePerPage" => 10000,
                                    "nextRacerTabEnabled" => 1,
                                    "finalResultsTime" => 15000,
                                    "showSequenceNumber" => 1,
                                    "showLapEstimation" => 0
                                );

                                foreach($defaultScoreboardSettings as $settingName => $settingValue)
                                {
                                    if (!isset($currentSlide['options'][$settingName]))
                                    {
                                        $currentSlide['options'][$settingName] = $settingValue;
                                    }
                                }
                            }
                            $newChannelFormat->timelines->races->slides[] = $currentSlide;
                        }
                        else
                        {
                            $newChannelFormat->timelines->regular->slides[] = $currentSlide;
                        }
                        $newChannelFormat->name = $channelData["name"];
                        $newChannelFormat->hash = $channelData["hash"];
                    }

                    //Insert the channel into the new table
                    $newChannelData = json_encode($newChannelFormat);
                    $stmt->bindParam(':ChannelNumber', $currentChannelID);
                    $stmt->bindParam(':ChannelData', $newChannelData);
                    $stmt->execute();

                    echo '<span style="color: green">Imported.</span><br/>';
                }

                echo '<p/>Import process complete!';
            }
            else
            {
                echo 'There are no old Speed Screen slides to import! Aborting import.';
            }
        }
        else
        {
            echo 'Invalid data received when querying for old Speed Screen slides. Aborting import.';
        }

    }

}

//Import new Channel Source setting if needed

echo '<p/>';

$settings = array(
    array(
        'Namespace'    => 'Speedscreen',
        'Name'         => 'channelSource',
        'Type'         => 'String',
        'DefaultValue' => "new",
        'Value'        => "new",
        'Description'  => '[CS Support Only] Which channels the Speed Screen should use. Can be "old" (the original channels in sp_admin) or "new" (the new, separate set of channels).',
        'IsPublic'     => true
    )
);

foreach($settings as $setting) {
    try {
        $existing = $db->settings->match(array(
            'Namespace' => $setting['Namespace']
        , 'Name'    => $setting['Name']
        ));
        if (empty($existing)) {
            $db->settings->create($setting);
            echo 'Setting (' . $setting['Namespace'] . ', ' . $setting['Name']  . ') successfully imported!';
            echo '<br>';
        }
        else {
            echo 'Setting (' . $setting['Namespace'] . ', ' . $setting['Name'] . ') already exists!';
            echo '<br>';
        }
    }
    catch (Exception $e) {
        echo 'Unable to import setting (' . $setting['Namespace'] . ', ' . $setting['Name'] . ')! ' . $e->getMessage();
        echo '<br>';
    }
}

echo '<p/>Done.';

die();