<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

$afterRaceSettingsJson = file_get_contents(__DIR__.'/resources/201411181527 - Default menuItems.json');


$settings = array(
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'featureIsEnabled',
    'Type'         => 'Boolean',
    'DefaultValue' => "0",
    'Value'        => "0",
    'Description'  => 'Only shows for support; allows them to enable/disable',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'postingIsEnabled',
    'Type'         => 'Boolean',
    'DefaultValue' => "0",
    'Value'        => "0",
    'Description'  => 'Allows the user to enable/disable Facebook posting',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'link',
    'Type'         => 'String',
    'DefaultValue' => '',
    'Value'        => '',
    'Description'  => 'The URL to the Facebook page of the track',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'message',
    'Type'         => 'String',
    'DefaultValue' => '',
    'Value'        => '',
    'Description'  => 'The message',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'photoUrl',
    'Type'         => 'String',
    'DefaultValue' => '',
    'Value'        => '',
    'Description'  => 'The URL to the photo that will be shown',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'name',
    'Type'         => 'String',
    'DefaultValue' => '',
    'Value'        => '',
    'Description'  => 'The name',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'description',
    'Type'         => 'String',
    'DefaultValue' => '',
    'Value'        => '',
    'Description'  => 'Additional details',
    'IsPublic'     => true
  ),
  array(
    'Namespace'    => 'FacebookAfterRace',
    'Name'         => 'caption',
    'Type'         => 'String',
    'DefaultValue' => '',
    'Value'        => '',
    'Description'  => 'The caption on the photo',
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


die('Successfully imported FacebookAfterRace settings and defaults.');
