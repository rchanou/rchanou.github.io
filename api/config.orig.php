<?php

// Configuration parameters...

$debugging = false;
$dateFormat = 'Y-m-d';
date_default_timezone_set('America/Los_Angeles'); // Full list: http://www.php.net/manual/en/timezones.php
$privateKey = rand() . time(); // Replace with a custom, hardcoded private key
$authentication_keys = array($privateKey, 'cs-dev', md5(date('Y-m-d')));
$apiUsername = 'PUT_USERNAME_HERE';
$apiPassword = 'PUT_PASSWORD_HERE';

// URL to images used for the digital signage aka "Speed Screen"
$channelImageUrl = '/sp_admin/ScreenImages/'; // This is hardcoded in Club Speed, cannot get elsewhere. :-(

// URL to HTML files for "Speed Screen"
$channelSlideUrl = '/api/slides/';

// Absolute Path to Customer Pictures (no trailing slash)
$customerPictureImagePath = 'C:\ClubSpeed\CustomerPictures';

// Absolute Path to Adult Signatures (no trailing slash)
$customerAdultSignatureImagePath = 'C:\ClubSpeed\CustomerSignatures';

// Absolute Path to Minor Signatures (no trailing slash)
$customerMinorSignatureImagePath = 'C:\ClubSpeed\CustomerSignatures';

// Absolute Path to Adult Waivers (no trailing slash)
$customerAdultWaiverImagePath = 'C:\ClubSpeed\CustomerWaivers';

// Absolute Path to Minor Waivers (no trailing slash)
$customerMinorWaiverImagePath = 'C:\ClubSpeed\CustomerWaivers';

// Translation Database
$translationDatabase = 'ClubspeedResource';

// Main Database
$defaultDatabase = 'ClubspeedV8';

// Logs Database
$logsDatabase = 'ClubSpeedLog';

// Override for the WebAPI cache clear - if truthy, then Queues logic will be run, regardless of current version number
$cacheClearOverride = false;