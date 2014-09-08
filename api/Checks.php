<?php

class Checks
{
    public $restler;
    private $logic;
    
    function __construct() {
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = $GLOBALS['logic'];
    }

    public function post($request_data = null) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // return $this->logic->checks->create($request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    public function get($id, $request_data = null) {
        if (!\ClubSpeed\Security\Validate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // return $this->logic->checks->get($id);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    public function put($id, $request_data = null) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // $this->logic->checks->update($id, $request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    public function delete($id) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // $this->logic->checks->delete($id);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    public function index($request_data = null) {
        if (!\ClubSpeed\Security\Validate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // if (\ClubSpeed\Utility\Params::hasNonReservedData($request_data)) {
            //     return $this->logic->checks->find($request_data);
            // }
            // else {
            //     return $this->logic->checks->all();
            // }
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}