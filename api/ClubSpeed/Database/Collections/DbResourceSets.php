<?php

namespace ClubSpeed\Database\Collections;

class DbResourceSets extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\ResourceSets');
        parent::__construct($db);
    }
}