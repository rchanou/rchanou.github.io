<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$settings = array(
    array(
        'Namespace'    => 'Speedscreen',
        'Name'         => 'defaultLocale',
        'Type'         => 'String',
        'DefaultValue' => "en-US",
        'Value'        => "en-US",
        'Description'  => 'The default language for the Speedscreen if none is specified in the URL',
        'IsPublic'     => true
    ),
    array(
        'Namespace'    => 'Speedscreen',
        'Name'         => 'apiDriver',
        'Type'         => 'String',
        'DefaultValue' => "polling",
        'Value'        => "polling",
        'Description'  => '[CS Support Only] The driver to use for the Speedscreen.',
        'IsPublic'     => true
    ),
    array(
        'Namespace'    => 'Speedscreen',
        'Name'         => 'channelUpdateFrequencyMs',
        'Type'         => 'Integer',
        'DefaultValue' => "60000",
        'Value'        => "60000",
        'Description'  => '[CS Support Only] How often the Speed Screen should check for a channel update, in milliseconds.',
        'IsPublic'     => true
    ),
    array(
        'Namespace'    => 'Speedscreen',
        'Name'         => 'racesPollingRateMs',
        'Type'         => 'Integer',
        'DefaultValue' => "3000",
        'Value'        => "3000",
        'Description'  => '[CS Support Only] How often the Speed Screen should check for races happening on any track that it needs to watch, in milliseconds. This is separate from the Scoreboard slide polling, and can be slower.',
        'IsPublic'     => true
    ),
    array(
        'Namespace'    => 'Speedscreen',
        'Name'         => 'timeUntilRestartOnErrorMs',
        'Type'         => 'Integer',
        'DefaultValue' => "30000",
        'Value'        => "30000",
        'Description'  => '[CS Support Only] How many milliseconds the Speed Screen should wait before restarting upon encountering any error.',
        'IsPublic'     => true
    ),
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


die('Successfully imported Speedscreen settings and defaults.');
