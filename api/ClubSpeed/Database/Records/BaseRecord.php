<?php

namespace ClubSpeed\Database\Records;

abstract class BaseRecord {

    public function validate($type) {
        return true; // override as necessary
    }

}