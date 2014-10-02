<?php

namespace ClubSpeed\Database\Collections;

class DbPayment extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Payment');
        parent::__construct($db);
    }
}