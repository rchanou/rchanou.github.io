<?php

class Passwords {

    public $restler;
    private $logic;
    
    function __construct(){
        // header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = isset($GLOBALS['logic']) ? $GLOBALS['logic'] : null;
    }

    public function post($id, $request_data = null) {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // post == "create"
            // use this to generate token and send email
            return $this->logic->passwords->create($request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    public function put($id, $request_data = null) {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // put == "update"
            // use this to check the token and run the reset?
            // use id?
            return $this->logic->passwords->reset($request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    // public function delete($id) {
    //     if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
    //         throw new RestException(401, "Invalid authorization!");
    //     }
    //     try {
    //         return $this->db->booking->delete($id);
    //     }
    //     catch (CSException $e) {
    //         throw new RestException($e->getCode() ?: 412, $e->getMessage());
    //     }
    //     catch (Exception $e) {
    //         throw new RestException(500, $e->getMessage());
    //     }
    // }

    // public function index($request_data = null) {
    //     if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
    //         throw new RestException(401, "Invalid authorization!");
    //     }
    //     try {
    //         return $this->logic->passwords->reset(\ClubSpeed\Utility\Params::nonReservedData($request_data));
    //     }
    //     catch (CSException $e) {
    //         throw new RestException($e->getCode() ?: 412, $e->getMessage());
    //     }
    //     catch (Exception $e) {
    //         throw new RestException(500, $e->getMessage());
    //     }
    // }
}