<?php

class TriggerLogs extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'TriggerLogs';
    }
}