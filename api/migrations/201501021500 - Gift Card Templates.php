<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$giftCardHtml = file_get_contents(__DIR__.'/resources/201501021500 - HTML01 - gift card.html');

$settings = array(
    array(
      'Namespace'    => 'Booking',
      'Name'         => 'giftCardEmailBodyHtml',
      'Type'         => 'HTML',
      'DefaultValue' => $giftCardHtml,
      'Value'        => $giftCardHtml,
      'Description'  => 'The HTML template to be used for gift card emails originating from the ClubSpeed PHP API'
    ),
    array(
      'Namespace'    => 'Booking',
      'Name'         => 'giftCardEmailSubject',
      'Type'         => 'String',
      'DefaultValue' => '{{business}} Gift Card',
      'Value'        => '{{business}} Gift Card',
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
