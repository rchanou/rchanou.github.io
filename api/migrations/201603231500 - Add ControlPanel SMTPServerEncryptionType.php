<?php
/**
 * Update the db schema for the OnlineBookingReservations CheckID
 */
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Utility\Convert;
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

$now = Convert::getDate();
$settings = array(
    array(
        'TerminalName' => 'MainEngine',
        'SettingName' => 'SMTPServerEncryptionType',
        'DataType' => '256',
        'DefaultSetting' => 'ssl',
        'SettingValue' => 'ssl',
        'Description' => 'The style of encryption to use when connecting to the mail server. Typically one of \'ssl\' or \'tls\'',
        'Fixed' => 0,
        'CreatedDate' => $now
    ),
    array(
        'TerminalName' => 'MainEngine',
        'SettingName' => 'SMTPMarketingServerEncryptionType',
        'DataType' => '256',
        'DefaultSetting' => 'ssl',
        'SettingValue' => 'ssl',
        'Description' => 'The style of encryption to use when connecting to the marketing mail server. Typically one of \'ssl\' or \'tls\'',
        'Fixed' => 0,
        'CreatedDate' => $now
    )
);
$interface = $db->controlPanel;

foreach ($settings as $setting) {
    try {
        $existing = $interface->match(array(
            'TerminalName' => $setting['TerminalName'],
            'SettingName' => $setting['SettingName']
        ));
        if (empty($existing)) {
            $interface->create($setting);
            echo 'Setting (' . $setting['TerminalName'] . ', ' . $setting['SettingName']  . ') successfully imported!';
            echo '<br>';
        }
        else {
            echo 'Setting (' . $setting['TerminalName'] . ', ' . $setting['SettingName'] . ') already exists!';
            echo '<br>';
        }
    }
    catch(Exception $e) {
        echo 'Unable to import control panel (' . $setting['TerminalName'] . ', ' . $setting['SettingName'] . ')! ' . $e->getMessage();
        echo '<br>';
    }
}
echo 'All control panel settings imported!';
die();
