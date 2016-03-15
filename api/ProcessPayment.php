<?php

use Clubspeed\Payments\PaymentService;
use ClubSpeed\Security\Authenticate;

class ProcessPayment {

    public $restler;
    private $logic;
    private $payments;
    
    function __construct() {
        header('Access-Control-Allow-Origin: *'); // include, since we aren't extending any base class
        $this->logic = $GLOBALS['logic'];
        $this->payments = new PaymentService($this->logic, $GLOBALS['db']);
    }

    /**
     * @url GET /
     */
    public function get($request_data = null) {
        if (!Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        return $this->payments->available();
    }

    /**
     * @url POST /
     */
    public function post($request_data = null) {
        if (!Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            return $this->payments->base->purchase($request_data);
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
            return $this->payments->base->completePurchase($request_data);
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}