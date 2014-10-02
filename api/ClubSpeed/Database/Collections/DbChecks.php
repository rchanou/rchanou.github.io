<?php

namespace ClubSpeed\Database\Collections;

class DbChecks extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Checks');
        parent::__construct($db);
    }
}