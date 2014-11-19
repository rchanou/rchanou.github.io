<?php
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$settings = array(
    array(
        'Namespace'    => 'Booking',
        'Name'         => 'termsAndConditions',
        'Type'         => 'HTML',
        'DefaultValue' => '<strong>Please contact our facility for our latest Terms & Conditions.</strong>',
        'Value'        => '<strong>Please contact our facility for our latest Terms & Conditions.</strong>',
        'Description'  => 'The html template to be used for Terms & Conditions text for the Booking application.'
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
die('Successfully imported booking templates.');
