<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

class Objects {

    /**
     * Dummy constructor to prevent any initialization of the Objects Class
     */
    private function __construct() {}

    public static function isEmpty($obj) {
        foreach($obj as $val) {
            if (isset($val))
                return false;
        }
        return true;
    }
}