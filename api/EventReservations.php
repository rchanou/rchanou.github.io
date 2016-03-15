<?php

class EventReservations extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'EventReservations';
    }
}