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

    public static function log($message, $namespace = null) {
        return self::$logger->log($message, $namespace);
    }

    public static function info($message, $namespace = null) {
        return self::$logger->info($message, $namespace);
    }

    public static function warn($message, $namespace = null) {
        return self::$logger->warn($message, $namespace);
    }

    public static function debug($message, $namespace = null) {
        return self::$logger->debug($message, $namespace);
    }

    public static function error($message, $namespace = null, \Exception $exception = null) {
        return self::$logger->error($message, $namespace, $exception);
    }
}