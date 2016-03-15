<?php

class EventTypes extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'EventTypes';
    }
}