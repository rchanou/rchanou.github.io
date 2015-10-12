<?php

// Configuration parameters...

$debugging = false;
$dateFormat = 'Y-m-d';
date_default_timezone_set('America/Los_Angeles'); // Full list: http://www.php.net/manual/en/timezones.php
$privateKey = rand() . time(); // Replace with a custom, hardcoded private key
$authentication_keys = array($privateKey, 'cs-dev', md5(date('Y-m-d')));
$apiUsername = 'PUT_USERNAME_HERE';
$apiPassword = 'PUT_PASSWORD_HERE';

/**
 * FINE GRAINED WHITELIST ACCESS CONTROL OVER API ENDPOINTS
 *
 * This access control is a whitelist and uses PHP Regex grant access to the requested HTTP_METHOD:URL.
 * "public" is a reserved "regex" to give access to any non-private resource.
 *
 * HOW THE REQUESTED URL IS CREATED AND MATCHED
 *  $requestedApiEndpoint = $_SERVER['REQUEST_METHOD'] . ':' . strtolower($_SERVER['PATH_INFO']);
 *  $foundMatch = preg_match($regex, $requestedApiEndpoint, $matches);
 *  If "$foundMatch" is true, the request is allowed.
 *  Regexes are processed in array order from 0 .. N
 *
 * EXAMPLE "$requestedApiEndpoint"s
 * - GET:/customers/1000001.json
 * - GET:/customers
 * - DELETE:/customers
 *
 * EXAMPLE REGEXS -- Test/Create with: http://www.phpliveregex.com/ */
 // "Private, all access": '/.*/' (this is the equivalent of a "$privateKey")
 // "All Public": "public" (this is a special keyword)
 // "READ Customers": "/GET:\/customers\/.*/"
 // "READ/CREATE Customers": "/[GET|POST]+:\/customers\/.*/"
 // "READ a Specific Customer": "/GET+:\/customers\/1000001.*/"

$aclKeys = array(
	// 'privateKeyHere' => array('regex1Here', 'regex2Here'),
);

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