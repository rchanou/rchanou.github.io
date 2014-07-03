<?php
class SimpleAuth implements iAuthenticate{

	function __isAuthenticated() {
		$keys = $GLOBALS['authentication_keys'];
		
		return isset($_GET['key']) && in_array($_GET['key'], $keys) ? true : false;
	}
	static function daily_key() {
		return md5(date('Y-m-d'));
	}
}