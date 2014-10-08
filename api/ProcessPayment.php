<?php

class ProcessPayment {

    public $restler;
    private $logic;
    private $payments;
    private $webapi;
    
    function __construct() {
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = $GLOBALS['logic'];
        $this->payments = new \ClubSpeed\Payments\PaymentService($this->logic);
        $this->webapi = $GLOBALS['webapi'];
    }

    public function get($request_data = null) {
        // for testing purposes, we can post back here as the server payment callback URL
        pr('inside processPayment get');
        pr($request_data);
        die();
    }

    public function post($id, $request_data = null) {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // // need a method for determining between purchase/authorize/completePurchase, etc.
            // $name = $request_data['name'];
            // if (strtolower($name) == 'pccharge') // hijack pccharge and send directly to the webapi, not omnipay
            //     return $this->webapi->processPayment($request_data);
            // else {
                // who determines what type of purchase this is? heat purchase? 
                return $this->payments->base->purchase($request_data);
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