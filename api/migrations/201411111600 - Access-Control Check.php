<?php
/**
 * This script checks if the web.config includes Access-Control settings
 */

// Load web.config and check for proper HTTP verbs inside of it
$path_to_api_webDotConfig = '..' . DIRECTORY_SEPARATOR . 'web.config';
$webDotConfig = file_get_contents($path_to_api_webDotConfig);
$occurrences = strpos($webDotConfig, 'Access-Control');

if(!$occurrences){
	echo 'NO ACCESS-CONTROL';
} else {
	echo 'ACCESS-CONTROL EXISTS';
}