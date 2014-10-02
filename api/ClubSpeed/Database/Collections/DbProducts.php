<?php

namespace ClubSpeed\Database\Collections;

class DbProducts extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Products');
        parent::__construct($db);
    }
}