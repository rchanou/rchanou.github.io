<?php

use ClubSpeed\Database\Helpers\UnitOfWork;

class HeatTypes extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'heatTypes';
    }
}