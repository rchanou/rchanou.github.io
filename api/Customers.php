<?php

class Customers extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\CustomersMapper();
        $this->interface = $this->logic->customers;
    }
}