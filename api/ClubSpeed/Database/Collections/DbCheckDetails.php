<?php

namespace ClubSpeed\Database\Collections;

class DbCheckDetails extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\CheckDetails');
        parent::__construct($db);
    }
}