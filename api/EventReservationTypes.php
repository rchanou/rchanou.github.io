<?php

class EventReservationTypes extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'EventReservationTypes';
    }
}
