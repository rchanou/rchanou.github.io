<?php

namespace ClubSpeed\Utility;

class Strings {

    /**
     * Dummy constructor to prevent any initialization of the Objects Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function split($str = '', $delimiter = ',') {
        if (empty($str))
            return array();
        return explode($delimiter, $str); // since i'm sick of trying to remember why PHP keeps swapping parameter order
    }

    public static function rangeToCSV($str) {
        $str = preg_replace('/\s+/', '', $str); // just get rid of all whitespace immediately
        $full = array();
        foreach(explode(',', $str) as $token) {
            if (strpos($token, '-')) {
                $split = explode('-', $token);
                if (!is_numeric($split[0]))
                    throw new \CSException('Start range provided in string was non-numeric! Received: ' . $split[0]);
                if (!is_numeric($split[1]))
                    throw new \CSException('End range provided in string was non-numeric! Received: ' . $split[1]);
                if (+$split[1] < +$split[0]) // note the conversion here
                    throw new \CSException('End range provided was greater than start range! Received start: ' . $split[0] . ' and end: ' . $split[1]);
                if ($split[0] === $split[1]) 
                    throw new \CSException('Start and end ranges provided were equal! Received start: ' . $split[0] . ' and end: ' . $split[1]);
                    // return $split[0]; // or do we want to convert this to a single token and not consider it an error?
                $full = array_merge($full, range($split[0], $split[1], 1));
            }
            else {
                if (!is_numeric($token))
                    throw new \CSException("Range token provided in string was non-numeric! Received: " . $token);
                $full[] = $token;
            }
        }
        sort($full);
        $return = implode(',', array_unique($full));
        return $return;
    }
}