<?php

class Booking extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\BookingMapper();
        $this->interface = $this->logic->booking;
    }
}