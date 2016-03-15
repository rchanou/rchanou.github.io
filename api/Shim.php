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
        if (!$this->webapi->canUse())
            throw new RestException(404); // consider the shim to be 'not found'
        // else, just return status 200
    }

    /**
     * @url POST /warmup
     */
    public function warmup() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(403, "Invalid authorization");
        try {
            $this->webapi->canUse(1); // fire off canUse, but only wait for 1 second.
        }
        catch(Exception $e) {
            // don't do anything with it.
            // note that this has a high chance of throwing
            // a timeout error or something similar,
            // which we want to ignore, since it will still
            // cause the webapi to start spinning up.
        }
    }

    /**
     * @url POST /clear_cache
     */
    public function clear_cache() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(403, "Invalid authorization");
        if (!$this->webapi->canUse())
            throw new RestException(404);
        try {
            $this->webapi->clearCache();
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}
