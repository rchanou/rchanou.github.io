<?php

use ClubSpeed\Database\Helpers\UnitOfWork;

class ActiveRaceLapCount extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'activeRaceLapCount';
    }
}