<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$html = file_get_contents(__DIR__.'/resources/201411041100 - HTML01 - receipt.html');
$text = file_get_contents(__DIR__.'/resources/201411041100 - TEXT01 - receipt.txt');
$subject = 'Receipt for Check #{{checkNumber}}';

$settings = array(
    array(
        'Namespace'    => 'Booking',
        'Name'         => 'receiptEmailBodyHtml',
        'Type'         => 'HTML',
        'DefaultValue' => $html,
        'Value'        => $html,
        'Description'  => 'The html template to be used for receipt emails originating from the ClubSpeed PHP API'
    ),
    array(
        'Namespace'    => 'Booking',
        'Name'         => 'receiptEmailBodyText',
        'Type'         => 'String',
        'DefaultValue' => $text,
        'Value'        => $text,
        'Description'  => 'The text template to be used for receipt emails originating from the ClubSpeed PHP API'
    ),
    array(
        'Namespace'    => 'Booking',
        'Name'         => 'receiptEmailSubject',
        'Type'         => 'String',
        'DefaultValue' => '{{businessName}} Receipt for Order Number: {{checkId}}',
        'Value'        => '{{businessName}} Receipt for Order Number: {{checkId}}',
        'Description'  => ''
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
