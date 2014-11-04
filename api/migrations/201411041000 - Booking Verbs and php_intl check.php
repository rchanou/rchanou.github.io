<?php
/**
 * This script checks the if the php_intl DLL is loaded and also checks if the web.config includes the proper verbs.
 */

// Check if php_intl is loaded
if(!extension_loaded('intl')) {
	echo '<h1 style="color: red;">No php_intl.dll loaded! Action Required!</h1>';
	echo '<p>Please edit "C:\Program Files\PHP\php.ini" and add the following line at the bottom:</p>';
	echo '<p><strong>extension=php_intl.dll</strong></p>';
} else {
	echo '<p style="color: green;">php_intl.dll IS loaded, no further action is necessary.</p>';
}

echo '<hr/>';

$server_version = php_uname();

if(strpos($server_version, 'Windows Server 2003')) {

	echo '<p>This appears to be a Windows 2003 Server -- ALL HTTP Verbs should be enabled by default for .php in the /api project.';

} else {

	// Load web.config and check for proper HTTP verbs inside of it
	$path_to_api_webDotConfig = '..' . DIRECTORY_SEPARATOR . 'web.config';
	$webDotConfig = file_get_contents($path_to_api_webDotConfig);
	$occurrences = strpos($webDotConfig, 'php" verb="GET,HEAD,POST,PUT,DELETE"');
	
	if(!$occurrences){
		echo '<h1 style="color: red;">HTTP Verbs Missing! Action Required!</h1>';
		echo '<p>Please edit "C:\ClubSpeedApps\api\web.config" on the line starting with: <em>&lt;add name="php-5.3.28"...</em> (php version may differ slightly)</p>';
		echo '<p>Replace <em>verb="GET,HEAD,POST"</em> to read: <strong>verb="GET,HEAD,POST,PUT,DELETE"</strong></p>';
	} else {
		echo '<p style="color: green;">PHP verbs for booking web.config are already in place, no further action is necessary.</p>';
	}

}