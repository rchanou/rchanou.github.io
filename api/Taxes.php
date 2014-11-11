<?php

class Taxes extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\TaxesMapper();
        $this->interface = $this->logic->taxes;
    }
}