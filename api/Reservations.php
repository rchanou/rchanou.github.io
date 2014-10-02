<?php

class Reservations extends BaseApi {

    function __construct(){
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\ReservationsMapper();
        $this->interface = $this->logic->reservations;
    }
}