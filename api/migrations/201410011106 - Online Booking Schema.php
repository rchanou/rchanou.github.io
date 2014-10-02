<?php
/**
 * Update the db schema for the Online Booking changes
 */
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$files = scandir('.');
$date = '201410011106'; // only grab sql files with this exact date
$sql_files = array_filter(scandir('.'), function($x) use ($date) {
    return ((strpos($x, $date) === 0) && (substr($x, -4) === '.sql'));
});

foreach($sql_files as $sql_file) {
    $sql = file_get_contents($sql_file);
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
die();