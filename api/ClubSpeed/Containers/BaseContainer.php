<?php

namespace ClubSpeed\Containers;

abstract class BaseContainer {

    public function __construct($data = array()) {
        $this->load($data);
    }

    public abstract function load(array $data = array());

}