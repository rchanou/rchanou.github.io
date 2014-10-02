<?php

namespace ClubSpeed\Database\Collections;

class DbCheckTotals_V extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\CheckTotals_V');
        parent::__construct($db);
    }
}