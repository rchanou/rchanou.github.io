<?php

class Locations extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'Locations';
    }
}
