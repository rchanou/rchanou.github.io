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
Log::info('Running ' . basename(__FILE__, '.php') . ' migrations', Enums::NSP_MIGRATIONS);

$resourceDir = './resources/';
$files = scandir($resourceDir);
$date = '201512091340'; // only grab sql files with this exact prefix
$sql_files = array_filter($files, function($file) use ($date) {
    return ((strpos($file, $date) === 0) && (substr($file, -4) === '.sql'));
});

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
    }
}
echo 'All schema updates complete!';
die();