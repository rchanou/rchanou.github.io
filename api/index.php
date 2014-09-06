<?php

// Configuration parameters...
require('config.php');



// Init ClubSpeed modules...
require_once('./ClubSpeed/cs-init.php');

if($debugging == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

set_include_path(get_include_path() . PATH_SEPARATOR . './restler');
spl_autoload_register('spl_autoload');

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat', 'JsonpFormat');
$r->addAPIClass('Karting', '');
$r->addAPIClass('Racers');
$r->addAPIClass('Races');
$r->addAPIClass('Reports');
$r->addAPIClass('Products');
$r->addAPIClass('Checks');
$r->addAPIClass('Subaru');
$r->addAPIClass('Tracks');
$r->addAPIClass('Users');
$r->addAPIClass('Version');
$r->addAPIClass('Channel');
$r->addAPIClass('Translations');
$r->addAPIClass('Settings');
$r->addAPIClass('Logs');
$r->addAPIClass('Events');
$r->addAPIClass('Queues');
$r->addAPIClass('Booking');
$r->addAPIClass('Reservations');
$r->addAPIClass('Passwords');
$r->addAPIClass('ScreenTemplate');
$r->addAPIClass('ScreenTemplateDetail');
// $r->addAuthenticationClass('SimpleAuth');
$r->handle();