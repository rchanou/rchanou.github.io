<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$html = file_get_contents(__DIR__.'/resources/201411181527 - HTML01 - track info.html');
$subject = 'Track Info';

$settings = array(
    array(
        'Namespace'    => 'MobileApp',
        'Name'         => 'receiptEmailBodyHtml',
        'Type'         => 'HTML',
        'DefaultValue' => $html,
        'Value'        => $html,
        'Description'  => 'The html template to be used for track info originating from the ClubSpeed PHP API'
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
die('Successfully imported receipt email templates.');
