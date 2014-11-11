<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$html = file_get_contents(__DIR__.'/resources/201410011133 - HTML01 - reset.html');
$text = file_get_contents(__DIR__.'/resources/201410011133 - TEXT01 - reset.txt');

$settings = array(
	array(
        'Namespace'    => 'Main',
        'Name'         => 'resetEmailBodyHtml',
        'Type'         => 'HTML',
        'DefaultValue' => $html,
        'Value'        => $html,
        'Description'  => 'The html template to be used for password reset emails originating from the ClubSpeed PHP API'
    ),
    array(
        'Namespace'    => 'Main',
        'Name'         => 'resetEmailBodyText',
        'Type'         => 'String',
        'DefaultValue' => $text,
        'Value'        => $text,
        'Description'  => 'The text template to be used for password reset emails originating from the ClubSpeed PHP API'
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