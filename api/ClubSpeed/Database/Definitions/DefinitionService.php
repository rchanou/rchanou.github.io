<?php

namespace ClubSpeed\Database\Definitions;

class DefinitionService {

    private function __construct() {} // prevent initialization of "static" class

    /**
     * An associative array used for storing instantiated ClubSpeed business classes.
     */
    private static $_lazy;

    public static function initialize() {
        self::$_lazy = array();
    }

    function __get($prop) {
        return $this->load($prop);
    }

    private function load($prop) {
        if (!empty(static::$_definition))
            return static::$_definition;
        $string = file_get_contents("ClubSpeed\\Database\\Definitions\\" . $prop . ".json"); 
        $json = json_decode($string, true);
        static::$_definition = $json;
        return static::$_definition;

        if (!isset($this->_lazy[$prop]))
            $this->_lazy[$prop] = new $prop($this, $this->db);
        

        $prop = '\ClubSpeed\Logic\\' . ucfirst($prop) . 'Logic'; // hacky -- we can go back to the old way detailed below, if desired
        if (!isset($this->_lazy[$prop]))
            $this->_lazy[$prop] = new $prop($this, $this->db);
        return $this->_lazy[$prop];
    }
}

DefinitionService::init(); // ensure that this gets executed when autoloaded