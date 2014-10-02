<?php

class ScreenTemplateDetail extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\ScreenTemplateDetailMapper();
        $this->interface = $this->logic->screenTemplateDetail;
    }
}