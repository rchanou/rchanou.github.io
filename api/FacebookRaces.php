<?php

use ClubSpeed\Enums\Enums as Enums;

class FacebookRaces extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\FacebookRacesMapper();
        $this->interface        = $this->logic->facebookRaces;
        $this->access['post']   = Enums::API_NO_ACCESS;
        $this->access['put']    = Enums::API_NO_ACCESS;
        $this->access['delete'] = Enums::API_NO_ACCESS;
    }
}