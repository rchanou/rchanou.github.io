<?php

// Configuration parameters...
require('config.php');

if($debugging == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

set_include_path(get_include_path() . PATH_SEPARATOR . './restler');
spl_autoload_register('spl_autoload');

// Init ClubSpeed modules after autoloader has been loaded
require_once('./ClubSpeed/ClubSpeedLoader.php');

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat', 'JsonpFormat');

$r->addAPIClass('Karting', ''); // index.php return

$r->addAPIClass('Booking');
$r->addAPIClass('BookingAvailability');
$r->addAPIClass('Channel');
$r->addAPIClass('CheckDetails');
$r->addAPIClass('Checks');
$r->addAPIClass('CheckTotals');
$r->addAPIClass('Events');
$r->addAPIClass('Logs');
$r->addAPIClass('Passwords');
$r->addAPIClass('ProcessPayment');
$r->addAPIClass('Products');
$r->addAPIClass('Queues');
$r->addAPIClass('Racers');
$r->addAPIClass('Races');
$r->addAPIClass('Reports');
$r->addAPIClass('Reservations');
$r->addAPIClass('ScreenTemplate');
$r->addAPIClass('ScreenTemplateDetail');
$r->addAPIClass('Settings');
$r->addAPIClass('Subaru');
$r->addAPIClass('Tracks');
$r->addAPIClass('Translations');
$r->addAPIClass('Users');
$r->addAPIClass('Version');

// $r->addAuthenticationClass('SimpleAuth');

$r->handle();