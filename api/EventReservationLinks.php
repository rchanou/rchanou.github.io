<?php

class EventReservationLinks extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'EventReservationLink';
    }
}