<?php

// Load ClubSpeed modules...

// for debugging purposes - can be removed after first pass at testing
require_once('./ClubSpeed/Utility/pr.php');

// for debugging purposes - can be removed after first pass at testing
require_once('./ClubSpeed/Utility/stopwatch.php');
$sw = $GLOBALS['sw'] = new \ClubSpeed\Utility\StopwatchStack();

// contains constants / "enums" for better readability
require_once('./ClubSpeed/Enums/cs-enums.php');

// loads all required underlying classes and attached db modules
require_once('./ClubSpeed/Database/csconnection.php'); 
$conn = $GLOBALS['conn'] = new \ClubSpeed\Database\CSConnection();

require_once('./ClubSpeed/Database/csdatabase.php');
$db = $GLOBALS['db'] = new \ClubSpeed\Database\CSDatabase($conn);

require_once('./ClubSpeed/Business/cs-logic.php');
$logic = $GLOBALS['logic'] = new \ClubSpeed\Business\CSLogic($db);

// handles validating API authenticated calls
require_once('./clubspeed/security/validate.php');

// inject the db connection into the static Validate class
\ClubSpeed\Security\Validate::initialize($logic);

// load the class for interfacing with the clubspeed webapi
require_once('./clubspeed/remoting/cs-webapi.php');
$webapi = $GLOBALS['webapi'] = new \ClubSpeed\Remoting\CSWebApi();