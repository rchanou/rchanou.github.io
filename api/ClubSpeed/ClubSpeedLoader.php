<?php

use ClubSpeed\Utility as Utility;
use ClubSpeed\Connection as Connection;
use ClubSpeed\Database as Database;
use ClubSpeed\Logic as Logic;
use ClubSpeed\Remoting as Remoting;
use ClubSpeed\Security\Authenticate as Authenticate;
use ClubSpeed\Logging\LogService as LogService;
use ClubSpeed\Mail\MailService as MailService;
use ClubSpeed\Payments\ProductHandlers\ProductHandlerService as ProductHandlerService;

// ensure the Composer AutoLoader is included
require_once(__DIR__.'/../vendors/autoload.php'); // ~ 4-5ms

// for debugging purposes - can be removed after first pass at testing
$sw = $GLOBALS['sw'] = new Utility\StopwatchStack();

// contains exception definitions for use throughout the application
require_once(__DIR__.'/Exceptions/Exceptions.php'); // STILL NEED TO FIX THIS FOR AUTOLOADER USE

$conn           = $GLOBALS['conn']          = new Connection\ClubSpeedConnection(); // ~ 1ms
$connResource   = $GLOBALS['connResource']  = new Connection\ClubSpeedConnection("(local)", "ClubSpeedResource"); // ~ 0ms
$connLogs       = $GLOBALS['connLogs']      = new Connection\ClubSpeedConnection("(local)", "ClubspeedLog"); // ~ 0ms
$db             = $GLOBALS['db']            = new Database\DbService($conn, $connResource, $connLogs); // ~ 0ms -- sort of hacky to inject all 3 -- if time allows, separate into 3 explicit contexts/services
$logic          = $GLOBALS['logic']         = new Logic\LogicService($db);  // ~ 1ms
$webapi         = $GLOBALS['webapi']        = new Remoting\WebApiRemoting($logic, $db);


// inject the LogicService into the static Authenticate class
Authenticate::initialize($logic); // ~ 0ms

// inject the LogicService->Logs interface into the static LogService class
LogService::initialize($logic->logs); // ~ 2-3ms

// inject the LogicService into the static MailService class, and name the desired MailInterface (lazy-loading)
MailService::initialize($logic, 'Swift'); // ~ 1ms

// inject the LogicService into the static ProductHandlerService class
ProductHandlerService::initialize($logic);