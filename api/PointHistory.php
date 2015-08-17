<?php

use ClubSpeed\Enums\Enums as Enums;

class PointHistory extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'PointHistory';
    }
}