<?php
class SimpleAuth implements iAuthenticate{

	function __isAuthenticated() {
        return true;
        // return \ClubSpeed\Security\Authenticate::isAuthorized();
	}
	static function daily_key() {
		return md5(date('Y-m-d'));
	}
}