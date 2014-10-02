<?php

namespace ClubSpeed\Database\Collections;

class DbOnlineBookingReservations extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookingReservations');
        parent::__construct($db);
    }
}