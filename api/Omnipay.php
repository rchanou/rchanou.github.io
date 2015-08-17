<?php

use Clubspeed\Payments\OmnipayService;
use ClubSpeed\Security\Authenticate;

class Omnipay {

    public $restler;
    private $logic;
    private $db;
    private $payments;
    
    function __construct() {
        header('Access-Control-Allow-Origin: *'); // include, since we aren't extending any base class
        $this->logic = $GLOBALS['logic'];
        $this->db = $GLOBALS['db'];
        $this->payments = new OmnipayService($this->logic, $this->db);
    }

    /**
     * @url GET /
     */
    public function get($request_data = null) {
        if (!Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        return $this->payments->available();
    }

    /**
     * @url GET /current
     */
    public function current($request_data = null) {
        if (!Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        return $this->payments->current();
    }

    /**
     * @url POST /purchase
     */
    public function purchase($request_data = null) {
        if (!Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        try {
            return $this->payments->purchase($request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
     * @url POST /complete
     */
    public function complete($request_data = null) {
        if (!Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        try {
            return $this->payments->complete($request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}