<?php

class ScreenTemplate
{
    public $restler;
    private $logic;
    
    function __construct(){
        header('Access-Control-Allow-Origin: *');
        $this->logic = $GLOBALS['logic'];
    }

    public function post($request_data = null) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            return $this->logic->screenTemplate->create($request_data);
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
            return $this->logic->screenTemplate->get($id);
        }
        catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function put($id, $request_data = null) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            $this->logic->screenTemplate->update($id, $request_data);
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
            $this->logic->screenTemplate->delete($id);
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
            if (\ClubSpeed\Utility\Params::hasNonReservedData($request_data)) {
                return $this->logic->screenTemplate->find($request_data);
            }
            else {
                return $this->logic->screenTemplate->all();
            }
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}