<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$namespace = 'SpeedText';

$settings = array(
	array(
        'Namespace'    => $namespace,
        'Name'         => 'isEnabled',
        'Type'         => 'Boolean',
        'DefaultValue' => '1', // or 'true'? SQL will store as nvarchar, either way.
        'Value'        => '1',
        'Description'  => 'Override flag to globally enable or disable SpeedText applications and processing.'
    ),
    array(
        'Namespace'    => $namespace,
        'Name'         => 'heatsPriorToSend',
        'Type'         => 'Integer',
        'DefaultValue' => '3',
        'Value'        => '3',
        'Description'  => 'The number of heats in advance to send SpeedTexts to registered racers.'
    ),
    array(
        'Namespace'    => $namespace,
        'Name'         => 'cutoffHour',
        'Type'         => 'Integer',
        'DefaultValue' => '4',
        'Value'        => '4',
        'Description'  => 'The hour at which the track closes. Used by SpeedText applications to determine when to stop and re-start sending messages.'
    ),
    array(
        'Namespace'    => $namespace,
        'Name'         => 'from',
        'Type'         => 'JSON',
        'DefaultValue' => '[]',
        'Value'        => '[]',
        'Description'  => 'The phone numbers from which to send SpeedTexts.'
    ),
    array(
        'Namespace'    => $namespace,
        'Name'         => 'message',
        'Type'         => 'String',
        'DefaultValue' => 'Please report to the Helmet / Briefing Area immediately. Your race will begin soon.',
        'Value'        => 'Please report to the Helmet / Briefing Area immediately. Your race will begin soon.',
        'Description'  => 'The SpeedText to send to racers prior to their race.'
    ),
    array(
        'Namespace'    => $namespace,
        'Name'         => 'provider',
        'Type'         => 'String',
        'DefaultValue' => 'twilio',
        'Value'        => 'twilio',
        'Description'  => 'The provider from which to send text messages.'
    ),
    array(
        'Namespace'    => $namespace,
        'Name'         => 'providerOptions',
        'Type'         => 'JSON',
        'DefaultValue' => '{}',
        'Value'        => '{}',
        'Description'  => 'The provider options to use to initialize the provider driver. These options will typically include usernames, keys, etc.'
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
die('Successfully imported password reset templates.');
