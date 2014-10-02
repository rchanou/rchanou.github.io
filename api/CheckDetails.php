<?php

class CheckDetails extends BaseApi {
    
    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\CheckDetailsMapper();
        $this->interface = $this->logic->checkDetails;
    }
}