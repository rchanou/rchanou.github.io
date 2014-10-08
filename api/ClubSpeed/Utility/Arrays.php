<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

class Arrays {

    /**
     * Dummy constructor to prevent any initialization of the Objects Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function &first(&$arr, $predicate) {
        $return = null;
        if (count($arr) > 0) {
            foreach($arr as $key => $val) {
                if (call_user_func($predicate, $val, $key, $arr)) {
                    return $arr[$key];
                }
            }
            // for ($i = 0; $i < count($arr); $i++) {
            //     if (call_user_func($predicate, $arr[$i])) {
            //         return $arr[$i];
            //     }
            // }
        }
        return $return;
    }
}