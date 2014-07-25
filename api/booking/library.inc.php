<?php
require('config.php');

if($debugging == true) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

// Include REST Library -- http://phphttpclient.com
include('./httpful.phar');
 
//$response = \Httpful\Request::get('http://google.com')->send();