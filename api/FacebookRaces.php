<?php

use ClubSpeed\Database\Helpers\UnitOfWork;

class FacebookRaces extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'facebookRaces';
    }
}