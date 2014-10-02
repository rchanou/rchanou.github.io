<?php

use ClubSpeed\Enums\Enums as Enums;

class Checks extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\ChecksMapper();
        $this->interface        = $this->logic->checks;
        $this->access['all']    = Enums::API_NO_ACCESS;
        $this->access['delete'] = Enums::API_NO_ACCESS;
    }
}