<?php

/**
 * Club Speed API Documentation template builder
 */
require_once(__DIR__.'/vendors/autoload.php');
require_once(__DIR__.'/ClubSpeed/Documentation/API/DocAPIContainer.php');

class Karting {

    public $restler;
    
	function __construct() {
		header('Access-Control-Allow-Origin: *'); //Here for all /say
	}

	public function index() {
    	// API Docs I Like: http://openbeerdatabase.com/
    	// http://hurl.it/
    	$daily_key = SimpleAuth::daily_key();

        $loader = new Twig_Loader_Filesystem('./views');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('karting.html'); // functions need to be defined before loading template
        $data = \ClubSpeed\Documentation\API\DocAPIContainer::getData();
        $data['daily_key'] = $daily_key;
        echo $template->render($data);
        die();
	}
}