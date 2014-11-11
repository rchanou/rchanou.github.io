<?php

class HeatMain extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\HeatMainMapper();
        $this->interface = $this->logic->heatMain; // already exists
    }
}