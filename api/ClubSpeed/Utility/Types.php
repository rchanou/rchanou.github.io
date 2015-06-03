<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

class Types {

    public static $array;
    public static $boolean;
    public static $date;
    public static $double;
    public static $integer;
    public static $null;
    public static $string;

    /**
     * Dummy constructor to prevent any initialization of the Types Class
     */
    private function __construct() {}

    public static function get($val) {
        if (is_object($val))
            return get_class($val);
        return gettype($val);
    }

    public static function byName($name) {
        // use sparingly. use Type::$type directly, whenever possible.
        $name = strtolower($name);
        switch($name) {
            case "boolean":
            case "bit":
                return Types::$boolean;
            case "date":
            case "datetime":
                return Types::$date;
            case "bigint":
            case "identity":
            case "int":
            case "integer":
                return Types::$integer;
            case "double":
            case "decimal":
            case "float":
            case "money":
            case "numeric":
            case "numericp":
                return Types::$double;
            case "ntext":
            case "varchar":
            case "nvarchar":
            case "string":
            case "uniqueidentifier":
                return Types::$string;
            case "null":
                return Types::$null;
        }
    }

    public static function init() {
        self::$array   = self::get(array());
        self::$boolean = self::get(TRUE);
        self::$date    = self::get(new \DateTime());
        self::$double  = self::get(1.1);
        self::$integer = self::get(1);
        self::$null    = self::get(NULL);
        self::$string  = self::get('foo');
    }
}

Types::init(); // call init automatically whenever autoloaded.