<?php

namespace ClubSpeed\Database\Collections;

class DbOnlineBookingAvailability_V extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookingAvailability_V');
        parent::__construct($db);
    }
}