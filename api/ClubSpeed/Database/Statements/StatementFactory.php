<?php

namespace ClubSpeed\Database\Statements;
use ClubSpeed\Database\Helpers\Comparator;

class StatementFactory {

    private static $map;

    private function __construct() {} // prevent construction of "static" class

    public static function register($key, $method) {
        self::$map[$key] = $method;
    }

    public static function init() {
        $operators = Comparator::$operators;
        self::$map = array();

        $baseFactory = function($args) { return new BaseStatement($args); };
        self::register('BASE', $baseFactory);

        $isFactory = function($args) { return new IsStatement($args); };
        self::register($operators['$is'], $isFactory);
        self::register($operators['$isnot'], $isFactory);

        $inFactory = function($args) { return new InStatement($args); };
        self::register($operators['$in'], $inFactory);
        self::register($operators['$notin'], $inFactory);

        $hasFactory = function($args) { return new HasStatement($args); };
        self::register($operators['$has'], $hasFactory);
    }

    public static function make($args = array()) {
        $factoryKey = $args['operator'];
        if (!isset(self::$map[$factoryKey]))
            $factoryKey = 'BASE';
        return call_user_func(self::$map[$factoryKey], $args);
    }
}

StatementFactory::init(); // call automatically whenever autoloaded