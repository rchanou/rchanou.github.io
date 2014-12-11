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
        // php allows us to check bool after the call 
        // to see if a cryptographically secure algorithm was properly used.
        // this shouldn't be a problem with any of our servers, though.
        return bin2hex(openssl_random_pseudo_bytes(32, $bool)); // returns 64 character string
    }
}