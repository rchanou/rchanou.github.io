<?php

/**
 * Club Speed API Documentation template builder
 */
require_once(__DIR__.'/vendors/autoload.php');
// require_once(__DIR__.'/ClubSpeed/Documentation/API/DocAPIContainer.php');

use ClubSpeed\Documentation\Api\DocAPIContainer;
use ClubSpeed\Templates\TemplateService as Templates;

class Karting {

    public $restler;
    
	function __construct() {
		// header('Access-Control-Allow-Origin: *'); //Here for all /say
	}

	public function index() {
    	// API Docs I Like: http://openbeerdatabase.com/
    	// http://hurl.it/
    	$daily_key = SimpleAuth::daily_key();
        DocAPIContainer::init();
        $data = DocAPIContainer::getData();
        $data['daily_key'] = $daily_key;
        echo Templates::build('karting.html', $data);
        die();
	}
}