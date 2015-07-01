<?php

class TriggerMemberships extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'TriggerMemberships';
    }
}