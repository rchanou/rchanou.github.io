<?php

namespace ClubSpeed\Database\Collections;

class DbCustomers extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Customers');
        parent::__construct($db);
    }
}