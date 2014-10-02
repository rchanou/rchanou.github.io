<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

class Objects {

    /**
     * Dummy constructor to prevent any initialization of the Objects Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function isEmpty($obj) {
        foreach($obj as $val) {
            if (isset($val))
                return false;
        }
        return true;
    }

    /**
     * Converts a provided dateString in the format of the client
     * to a new dateString in the format of the server/database.
     *
     * @param string $dateString 
     * @param string dateFormat (optional) The format to use as an override for the final result for the server. Note that this is typically already defined in config.php.
     * @return string The dateString in the format which the database expects.
     */
    public static function toDateForServer($dateString, $dateFormat = null) {
        if (is_null($dateString))
            return Enums::DB_NULL;
            // return $dateString; // don't attempt to convert nulls
        
        if (is_string($dateString)) {
            $dateArray = date_parse_from_format(self::DATE_FORMAT_FROM_CLIENT, $dateString);
            // need a method for trapping errors here and recording them, or just fail?
            // if ($dateArray["error_count"] == 0) {
                // check to make sure we have a valid month/day/year combination
                // (checks for leap years, number of days in a specific month, etc)
                if (checkdate($dateArray['month'], $dateArray['day'], $dateArray['year'])) {
                    // convert to php date
                    // use either the dateFormat argument (if provided),
                    // the dateFormat global (if available),
                    // or a fallback if all else fails
                    $date = date(
                        // $dateFormat ?: self::DATE_FORMAT_FROM_CLIENT ?: "Y-m-d H:i:s"
                        $dateFormat ?: $GLOBALS['dateFormat'] ?: 'Y-m-d H:i:s' // todo, grab a better default
                        , mktime(
                            $dateArray['hour']
                            , $dateArray['minute']
                            , $dateArray['second']
                            , $dateArray['month']
                            , $dateArray['day']
                            , $dateArray['year']
                        )
                    );
                    return $date;
                }
            // }
        }
        throw new \InvalidArgumentException("Convert::toDateForServer was unable to convert the provided string! Received: " . $dateString);
        // return null; // what to return in the case of a failure (???)
    }

    private static function is_null_or_db_null($val) {
        if (is_null($val))
            return true;
        if ($val === Enums::DB_NULL)
            return true;
        return false;
    }

    public static function toBoolean($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public static function toNumber($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return +$val;
    }

    public static function toString($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return (string)$val;
    }
}