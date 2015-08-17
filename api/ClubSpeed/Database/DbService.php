<?php

namespace ClubSpeed\Database;

/**
 * The ClubSpeed database object 
 * used to contain all underlying DbCollections.
 */
class DbService {

    private $conn;
    private $connResource;
    private $connLogs;
    private $_lazy;

    public function __construct(&$CSConnection, &$ResourceConnection, &$LogConnection) {
        // should this secondary connection be in here, or should we have a separate csdatabase-style instance for the resource items?
        // note that if we don't inject two connections here, we will still have to inject two databases into the logic class anyways
        // also note that if we do not separate the database classes, then passthrough direct methods (such as query and exec) will always to go ClubSpeedV8

        $this->conn = $CSConnection;
        $this->connResource = $ResourceConnection;
        $this->connLogs = $LogConnection;
        $this->_lazy = array();
    }

    function __get($prop) {
        return $this->load($prop);
    }

    private function load($prop) {
        switch(strtolower($prop)) {
            // hacky way to select the right connection
            // will split into 3 separate contexts when/if time allows
            case 'resourcesets':
                $conn =& $this->connResource; // needs to use the resource connection injection
                $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
                break;
            case 'logs':
                $conn =& $this->connLogs; // needs to use the logs connection injection
                $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
                break;
            case 'fb_customers_new':
                $conn =& $this->conn;
                $record = __NAMESPACE__ . '\Records\\FB_Customers_New'; // doesn't match the table naming scheme, load explicitly
                break;
            default:
                $conn =& $this->conn;
                $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
                break;
        }
        // $record = __NAMESPACE__ . '\Records\\' . ucfirst($prop);
        if (!isset($this->_lazy[$prop]))
            $this->_lazy[$prop] = new DbCollection($conn, $record);
        return $this->_lazy[$prop];
    }

    public function query($sql, $params = array()) {
        return $this->conn->query($sql, $params); // pass through
    }

    public function exec($sql, $params = array()) {
        return $this->conn->exec($sql, $params); // pass through
    }

    public function begin() {
        return $this->conn->begin();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollback();
    }
}