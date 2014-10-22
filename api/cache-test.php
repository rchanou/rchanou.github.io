<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Init cache
require_once("./vendors/phpfastcache/phpfastcache.php");
phpFastCache::setup("storage", "files");

$cacheName = 'getScoreboard|trackId=|heatNum=200';

// Initialize cache
$cache = phpFastCache();

// Try to get from cache
$getScoreboard = $cache->get($cacheName);
if($getScoreboard !== null) {
		echo json_encode($getScoreboard);
}

//
return 'cache miss';