<?php

// Configuration parameters...
require('config.php');

if(@$debugging == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

/*
    Handle CORS at the root index.php level.

    If we see OPTIONS make it through IIS, which requires
    that web.config contains the OPTIONS verb at the PHP level,
    then set the headers and return the pre-flight request immediately.

    If not, allow the request to continue through with the CORS headers set.
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE,GET,OPTIONS,POST,PUT');
header('Access-Control-Allow-Headers: Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
    die();

set_include_path(get_include_path() . PATH_SEPARATOR . './restler');
spl_autoload_register('spl_autoload');

require_once('./ClubSpeed/ClubSpeedLoader.php');

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat', 'JsonpFormat');

$r->addAPIClass('Karting', ''); // index.php return

$r->addAPIClass('ActiveRaceLapCount');
$r->addAPIClass('AuthenticationTokens');
$r->addAPIClass('Booking');
$r->addAPIClass('BookingAvailability');
$r->addAPIClass('Channel');
$r->addAPIClass('CheckDetails');
$r->addAPIClass('Checks');
$r->addAPIClass('CheckTotals');
$r->addAPIClass('ControlPanel');
$r->addAPIClass('Countries');
$r->addAPIClass('Customers');
$r->addAPIClass('CustomerStatus');
$r->addAPIClass('Definition');
$r->addAPIClass('DiscountType');
$r->addAPIClass('Events');
$r->addAPIClass('Facebook');
$r->addAPIClass('FacebookRaces');
$r->addAPIClass('GiftCardBalance');
$r->addAPIClass('GiftCardHistory');
$r->addAPIClass('HeatDetails');
$r->addAPIClass('HeatMain');
$r->addAPIClass('HeatTypes');
$r->addAPIClass('Logs');
$r->addAPIClass('MembershipTypes');
$r->addAPIClass('Passwords');
$r->addAPIClass('Payments');
$r->addAPIClass('PointHistory');
$r->addAPIClass('PrimaryCustomers');
$r->addAPIClass('ProcessPayment');
$r->addAPIClass('Products');
$r->addAPIClass('Queues');
$r->addAPIClass('Racers');
$r->addAPIClass('Races');
$r->addAPIClass('Reports');
$r->addAPIClass('Reservations');
$r->addAPIClass('ResourceSets');
$r->addAPIClass('ScreenTemplate');
$r->addAPIClass('ScreenTemplateDetail');
$r->addAPIClass('Settings');
$r->addAPIClass('Shim');
$r->addAPIClass('Sources');
$r->addAPIClass('SpeedScreenChannels');
$r->addAPIClass('Subaru');
$r->addAPIClass('Taxes');
$r->addAPIClass('Tests');
$r->addAPIClass('Tracks');
$r->addAPIClass('Translations');
$r->addAPIClass('TriggerLogs');
$r->addAPIClass('TriggerMemberships');
$r->addAPIClass('Users');
$r->addAPIClass('UserTasks');
$r->addAPIClass('Version');

// $r->addAuthenticationClass('SimpleAuth');
$r->handle();
