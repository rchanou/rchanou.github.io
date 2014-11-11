<?php

namespace ClubSpeed\Logging;

class LogService {

    protected static $logger; // internal reference to the LogInterface being used

    private function __construct() {} // prevent initialization of "static" class

    public static function initialize(&$logger) {
        if (!$logger instanceof LogInterface)
            throw new \InvalidArgumentException("Attempted to initialize LogService without a LogInterface!");
        self::$logger = $logger;
    }

    public static function log($message) {
        return self::$logger->log($message);
    }

    public static function warn($message) {
        return self::$logger->warn($message);
    }

    public static function debug($message) {
        return self::$logger->debug($message);
    }

    public static function error($message, \Exception $exception = null) {
        return self::$logger->error($message, $exception);
    }
}