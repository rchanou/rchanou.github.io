<?php
/**
 * Update the db schema for the Online Booking changes
 */
require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$resourceDir = './resources/';
$files = scandir($resourceDir);
$date = '201503101140'; // only grab sql files with this exact prefix
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