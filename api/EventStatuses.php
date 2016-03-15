<?php

class EventStatuses extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'EventStatus';
    }
}