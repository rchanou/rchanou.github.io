<?php

namespace ClubSpeed\Logic;

/**
 * The service container
 * for all logic classes.
 */
class LogicService {

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * An associative array used for storing instantiated ClubSpeed business classes.
     */
    private $_lazy;
    
    /**
     * Constructs a new instance of the CSBooking class.
     *
     * The CSBooking constructor requires an instantiated CSConnection class for injection.
     *
     * @param CSConnection $db The CSConnection class to inject.
     */
    public function __construct(&$db) {
        $this->db = $db;
        $this->_lazy = array();
    }

    function __get($prop) {
        return $this->load($prop);
    }

    private function load($prop) {
        $prop = '\ClubSpeed\Logic\\' . ucfirst($prop) . 'Logic'; // hacky -- we can go back to the old way detailed below, if desired
        if (!isset($this->_lazy[$prop]))
            $this->_lazy[$prop] = new $prop($this, $this->db);
        return $this->_lazy[$prop];
    }
}