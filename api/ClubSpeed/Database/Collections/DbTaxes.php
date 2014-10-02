<?php

namespace ClubSpeed\Database\Collections;

class DbTaxes extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Taxes');
        parent::__construct($db);
    }
}