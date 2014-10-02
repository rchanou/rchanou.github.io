<?php

use ClubSpeed\Utility as Utility;
use ClubSpeed\Connection as Connection;
use ClubSpeed\Database as Database;
use ClubSpeed\Logic as Logic;
use ClubSpeed\Remoting as Remoting;

// for debugging purposes - can be removed after first pass at testing
$sw = $GLOBALS['sw'] = new Utility\StopwatchStack();

// contains exception definitions for use throughout the application
require_once(__DIR__.'/Exceptions/Exceptions.php'); // STILL NEED TO FIX THIS FOR AUTOLOADER USE

$conn           = $GLOBALS['conn']          = new Connection\ClubSpeedConnection();
$connResource   = $GLOBALS['connResource']  = new Connection\ClubSpeedConnection("(local)", "ClubSpeedResource");
$db             = $GLOBALS['db']            = new Database\DbService($conn, $connResource);
$logic          = $GLOBALS['logic']         = new Logic\LogicService($db);
$webapi         = $GLOBALS['webapi']        = new Remoting\WebApiRemoting($logic);

// inject the LogicService into the static Authenticate class
\ClubSpeed\Security\Authenticate::initialize($logic);