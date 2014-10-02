<?php

namespace ClubSpeed\Database\Collections;

class DbAuthenticationTokens extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\AuthenticationTokens');
        parent::__construct($db);
    }
}