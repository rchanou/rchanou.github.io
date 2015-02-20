<?php

namespace ClubSpeed\Mappers;

/**
 * The service container
 * for all logic classes.
 */
class MapperService {

    /**
     * An associative array used for storing instantiated ClubSpeed business classes.
     */
    private $_lazy;

    // this works here, since MapperService doesn't require any arguments (like LogicService's $db),
    // but it sort of breaks the pattern we are using everywhere else, with dependency injection.
    // public static function instance() {
    //     static $_instance; // store here for inheritance reasons
    //     if (is_null($_instance))
    //         $_instance = new MapperService();
    //     return $_instance;
    // }
    
    public function __construct() { // protect constructor
        $this->_lazy = array();
    }
    // private function __clone() {}  // disallow cloning
    // private function __wakeup() {} // disallow deserialization

    function __get($prop) {
        return $this->load($prop);
    }

    protected function load($prop) {
        $prop = '\ClubSpeed\Mappers\\' . ucfirst($prop) . 'Mapper'; // dirrrrrrty. but works.
        if (!isset($this->_lazy[$prop]))
            $this->_lazy[$prop] = new $prop();
        return $this->_lazy[$prop];
    }
}