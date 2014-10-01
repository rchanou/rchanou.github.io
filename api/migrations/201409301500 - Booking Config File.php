<?php
/**
 * This script installs the config.php for the new booking site using
 * the private key from the API.
 */

$path_to_booking_config_directory = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'booking' . DIRECTORY_SEPARATOR . 'laravel' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;

// See if we need to rename config.php
$booking_config_file = $path_to_booking_config_directory . 'config.php';
if(file_exists($booking_config_file)) {
    // If it exists but is 0 bytes, we should delete it
		if(filesize( $booking_config_file) == 0) {
			echo 'Config file is 0 bytes -- removing and replacing with config.orig.php<br/>';
			unlink($booking_config_file);
			copy($path_to_booking_config_directory . 'config.orig.php', $booking_config_file);
		} else {
			die($path_to_booking_config_directory . 'config.php already exists. Exiting!');
		}
}

// Get private key
require_once('../config.php');
if(!isset($privateKey)) {
	die('$privateKey is not set! Exiting!');
}

// Get configuration template
$configuration_template = file_get_contents($path_to_booking_config_directory . 'config.orig.php');

// Replace INSERT_PRIVATE_KEY_HERE with API $privateKey in configuration template
$configuration_template = str_replace('INSERT_PRIVATE_KEY_HERE', $privateKey, $configuration_template);

// Replace date format with $dateFormat from API configuration
$configuration_template = str_replace('Y-m-d', $dateFormat, $configuration_template);

// Write configuration template
file_put_contents($path_to_booking_config_directory . 'config.php', $configuration_template);

// Confirm success
die('Successfully wrote booking configuration');