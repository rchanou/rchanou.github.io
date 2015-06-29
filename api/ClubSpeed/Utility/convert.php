<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

class Convert {

    /**
     * @const The date format expected from the client for any given API call.
     *
     * Note: The GLOBALS['dateFormat'] is the expected format for sending to the database.
     */
    const DATE_FORMAT_FROM_CLIENT = 'Y-m-d\TH:i:s.u';
    const ISO_FOR_MSSQL_DATETIME = 'Y-m-d\TH:i:s'; // note that 2 digits of fractional second precision will be added manually.

    // date problems.
    // 1. SQL can support YYYY-mm-ddTHH:mm:ss.sTZD, but needs to be datetime2(7) or datetimeoffset(7). we have datetime's scattered throughout the database.
    //    1a. datetime can support YYYY-mm-ddTHH:mm:ss.SSS, as long as string does not have more than 3 digits of precision.
    //    1b. datetime isn't TRUE 3 digits of precision -- always rounded to the nearest 0.##0, 0.##3, or 0.##7. woo.
    // 2. Javascript doesn't parse properly unless timezone (TZD) is provided.
    // 3. PHP has issues with date time microsecond precision, unless using the new \DateTime class.
    
    // const DATE_FORMAT_WISHFUL = 'Y-m-d\TH:i:s.uP'; // would be usable by both sql and javascript.
    // and by usable by sql, i mean datetime throws exceptions,
    // datetime2 ignores the timezone portion (P) and still inserts,
    // and datetimeoffset handles properly, but we can't use it (does not exist in 2005. hooray).

    
    /**
     * Dummy constructor to prevent any initialization of the Validate Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function getDate($time = null) {
        $date = new \DateTime();
        if (!empty($time) && is_integer($time))
            $date->setTimestamp($time);
        return self::toDateForServer($date);
    }

    /**
     * Converts a provided dateString in the format of the client
     * to a new dateString in the format of the server/database.
     *
     * @param any $date 
     * @param string dateFormat (optional) The format to use as an override for the final result for the server. Note that this is typically already defined in config.php.
     * @return string The dateString in the format which the database expects.
     */
    public static function toDateForServer($date, $dateFormat = null) {
        if (self::is_null_or_db_null($date))
            return Enums::DB_NULL;
        if (is_string($date)) {
            if (empty($date))
                return ""; // shortcut? will most likely cause a sql exception, but really should never be hit.
            $date = new \DateTime($date);
        }
        // aim to have \DateTime by this point.
        if ($date instanceof \DateTime) {
            $date->setTimezone(new \DateTimeZone(date_default_timezone_get())); // if we have a Z/UTC timezone, convert to local time, defined by API's config.php
            $datetimeString = $date->format(self::ISO_FOR_MSSQL_DATETIME);
            $microseconds = Convert::toNumber($date->format('u')); // 'u' grabs microseconds only.
            // since mssql datetime type rounds to the nearest 0.##0, 0.##3, or 0.##7 (bits/precision issue),
            // opting for rounding to the nearest predictable / reproducible digit (0.##)
            $roundedMicroseconds = round($microseconds / 10000); // only grab two digits of precision. could also leave as 0.### and chop off the leading 0 before concat
            // $datetimeMicroseconds = str_pad($roundedMicroseconds, 2, '0', \STR_PAD_LEFT); // this works too. doesn't seem to be faster.
            $datetimeMicroseconds = sprintf("%02d", $roundedMicroseconds); // make sure we zero-pad to 2 characters.
            $datetimeFullString = $datetimeString . '.' . $datetimeMicroseconds;
            return $datetimeFullString;
        }
        throw new \InvalidArgumentException("Convert::toDateForServer was unable to convert the provided item! Received: " . print_r($date, true));
        // return null; // what to return in the case of a failure (???)
    }

    private static function is_null_or_db_null($val) {
        if (is_null($val))
            return true;
        if ($val === Enums::DB_NULL)
            return true;
        return false;
    }

    public static function convert($val, $type) {
        if(isset($type) && is_string($type)) {
            switch($type) {
                case Types::$boolean:
                    return self::toBoolean($val);
                case Types::$date:
                    return self::toDate($val);
                case Types::$double:
                    return self::toDouble($val);
                case Types::$integer:
                    return self::toInteger($val);
                case Types::$string:
                    return self::toString($val);
                case Types::$null:
                    return null; // something else?
                default:
                    $type = Types::byName($type); // see if we got passed a name that doesn't quite match properly.
                    if (!empty($type)) // if we did, then take another run through.
                        return Convert::convert($val, $type);
            }
        }
        return $val; // unknown type, just return the original
    }

    public static function toBoolean($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public static function toDouble($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return (double)$val;
    }

    public static function toDate($date, $dateFormat = null) {
        // return $date; // for testing purposes
        return self::toDateForServer($date, $dateFormat);
    }

    public static function toInteger($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return (int)$val;
    }

    public static function toNumber($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return +$val; // consider a method for int vs double conversion
    }

    public static function toString($val) {
        if (self::is_null_or_db_null($val))
            return $val;
        return (string)$val;
    }
}