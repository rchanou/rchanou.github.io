<?php

class Shim {

    protected $webapi;
    
    function __construct() {
        $this->webapi = $GLOBALS['webapi'];
    }
    
    /**
     * @url GET /
     */
    public function get() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(403, "Invalid authorization!");
        if (!$this->webapi->canUse()) {
            throw new RestException(404); // consider the shim to be 'not found'
        }
        // else, just return status 200
    }
}