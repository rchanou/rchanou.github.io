<?php
/**
 * This script installs the config.php for the new admin panel using
 * the private key from the API.
 */

$path_to_admin_config_directory = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'laravel' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;

// See if we need to run checks on an existing config.php
$admin_config_file = $path_to_admin_config_directory . 'config.php';
if(file_exists($admin_config_file)) {

    // If file has INSERT_PRIVATE_KEY_HERE, delete it
		if(strpos(file_get_contents($admin_config_file), 'INSERT_PRIVATE_KEY_HERE')) {

			echo 'Config file does not have private key set -- removing and replacing with config.orig.php<br/>';
			unlink($admin_config_file); // It exists but is not setup, delete it and remake
			copy($path_to_admin_config_directory . 'config.orig.php', $admin_config_files);

		} elseif(filesize($admin_config_file) == 0) { // It exists but is 0 bytes, we should delete it

			echo 'Config file is 0 bytes -- removing and replacing with config.orig.php<br/>';
			unlink($admin_config_file);
			copy($path_to_admin_config_directory . 'config.orig.php', $admin_config_files);

		} else { // File exists and has been edited

			die($path_to_admin_config_directory . 'config.php already exists. Exiting!');

		}

}

// Get private key
require_once('../config.php');
if(!isset($privateKey)) {
	die('$privateKey is not set! Exiting!');
}

// Get configuration template
$configuration_template = file_get_contents($path_to_admin_config_directory . 'config.orig.php');

// Replace INSERT_PRIVATE_KEY_HERE with API $privateKey in configuration template
$configuration_template = str_replace('INSERT_PRIVATE_KEY_HERE', $privateKey, $configuration_template);

// Replace date format with $dateFormat from API configuration
$configuration_template = str_replace('Y-m-d', $dateFormat, $configuration_template);

// Write configuration template
file_put_contents($path_to_admin_config_directory . 'config.php', $configuration_template);

// Confirm success
die('Successfully wrote admin configuration');