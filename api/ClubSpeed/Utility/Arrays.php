<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

class Arrays {

    /**
     * Dummy constructor to prevent any initialization of the Arrays Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function contains($arr, $predicate) {
        if (count($arr) > 0) {
            foreach($arr as $key => $val) {
                if (call_user_func($predicate, $val, $key, $arr))
                    return true;
            }
        }
        return false;
    }

    public static function &first(&$arr, $predicate = null) {
        $return = null;
        if (is_null($predicate) || empty($predicate))
            $predicate = function($x) { return $x; };
        if (count($arr) > 0) {
            foreach($arr as $key => $val) {
                if (call_user_func($predicate, $val, $key, $arr))
                    return $arr[$key];
            }
        }
        return $return;
    }

    public static function &group($arr, $keySelector, $comparator = null) {
        // expected end grouped structure detailed below
        //
        // note that we are not using associative arrays
        // in case the $keySelector is grouping
        // by multiple keys, or non primitives
        //
        // -- return structure --
        // array(
        //     array(
        //         // group 1 item 1
        //         // group 1 item 2
        //     ),
        //     array (
        //         // group 2 item 3
        //         // group 2 item 4
        //     )
        // )

        if (!is_array($arr))
            return $arr;
        if (is_null($keySelector) || !is_callable($keySelector))
            return $arr; // or set default? default doesn't make a lot of sense here
        if (is_null($comparator) || !is_callable($comparator)) {
            $comparator = function($a, $b) {
                if ($a == $b)
                    return 0;
                else if ($a < $b)
                    return -1;
                else 
                    return 1;
            };
        }
        
        $grouped = array();
        foreach($arr as $val) {
            $keys = $keySelector($val);
            $existing =& Arrays::first($grouped, function($group) use ($keySelector, $keys) {
                // note that $group[0] is sufficient, as each item in $group
                // should always return the same thing from $keySelector
                return $keySelector($group[0]) == $keys; 

                //// we could also use the code below, but this is much more inefficient
                // return Arrays::contains($x, function($y) use ($keySelector, $keys) {
                //     return $keySelector($y) == $keys;
                // });
            });
            if (is_null($existing)) {
                // could not find the existing group containing the keys - make a new array for this group, and place $val inside it
                $grouped[] = array($val);
            }
            else {
                // found the existing group containing the keys - append $val to the end of that group
                $existing[] = $val;
            }
        }
        return $grouped;
    }

    public static function isAssociative($arr) {
        if (is_array($arr)) {
            foreach($arr as $key => $val) {
                if (!is_int($key))
                    return true;
            }
        }
        return false;
    }

    public static function &mapRecursive($arr, $predicate) {
        foreach($arr as $key => $val) {
            if (is_array($val))
                $arr[$key] = self::$mapRecursive($val, $predicate);
            else
                $arr[$key] = call_user_func($predicate, $val, $key, $arr);
        }
        return $arr; // copy or pointers?
    }

    public static function reduce(&$arr, $reducer, $seed=null) {
        return array_reduce($arr, $reducer, $seed); // no need to rewrite. just to keep the interface sane/consistent.
    }

    public static function select(&$arr, $selector) {
        $return = array();
        foreach($arr as $key => &$val)
            $return[] = call_user_func($selector, $val, $key, $arr);
        return $return;
    }

    public static function &where(&$arr, $predicate) {
        $return = array();
        foreach($arr as $key => &$val) {
            // if only we had the yield keyword..
            if (call_user_func($predicate, $val, $key, $arr))
                $return[] =& $val;
        }
        return $return;
    }
}