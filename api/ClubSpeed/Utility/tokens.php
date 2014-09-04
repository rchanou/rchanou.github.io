<?php

namespace ClubSpeed\Utility;

class Tokens {

    /**
     * Dummy constructor to prevent any initialization of the Tokens Class
     */
    private function __construct() {} // prevent any initialization of this class

    /**
     * Document: TODO
     */
    public static function generate() {
        return sha1(uniqid('', true)); // best practice? possibly seed with username?
    }
}