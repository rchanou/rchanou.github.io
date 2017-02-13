<?php

class EventTaskTypes extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'TasksForCheckEvent'; // note the intentional mismatch
    }
}
