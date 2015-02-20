<?php

use ClubSpeed\Database\Helpers\UnitOfWork;

class Logs extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'logs';
    }
}