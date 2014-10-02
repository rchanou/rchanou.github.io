<?php

class ScreenTemplate extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\ScreenTemplateMapper();
        $this->interface = $this->logic->screenTemplate;
    }
}