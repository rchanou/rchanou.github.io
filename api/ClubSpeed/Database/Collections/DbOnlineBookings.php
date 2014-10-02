<?php

namespace ClubSpeed\Database\Collections;

class DbOnlineBookings extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookings');
        parent::__construct($db);
    }
}