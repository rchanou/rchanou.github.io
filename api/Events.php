<?php

class Events extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'Events';
    }
}