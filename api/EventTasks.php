<?php

class EventTasks extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'CheckEventTasks'; // intentional mismatch
    }
}
