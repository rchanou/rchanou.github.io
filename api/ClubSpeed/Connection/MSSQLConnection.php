<?php

namespace ClubSpeed\Connection;
require_once(__DIR__.'./BaseConnection.php');

/**
 * The MSSQL implementation of DbConnection.
 */
class MSSQLConnection extends \ClubSpeed\Connection\BaseConnection {

    /**
     * A private reference to the PDO type. Defaulted to 'sqlsrv' in this instance.
     */
    private $_type;

    /**
     * A private reference to the PDO server provided by the constructor.
     */
    private $_server;

    /**
     * A private reference to the PDO database provided by the constructor.
     */
    private $_database;

    /**
     * A private reference to the PDO username provided by the constructor.
     */
    private $_username;

    /**
     * A private reference to the PDO password provided by the constructor.
     */
    private $_password;

    /**
     * Creates a new instance of the MSSQLConnection class.
     *
     * @param string    $server     The server to which to connect. If none is provided, then (local) is used.
     * @param string    $database   The database instance to use as a default. If none is provided, then ClubSpeedV8 is used.
     * @param string    $username   The username to use for credentials. If not provided, then integrated windows authentication will be used.
     * @param string    $password   The password to use for credentials. If not provided, then integrated windows authentication will be used.
     */
    public function __construct($server, $database, $username, $password) {
        $this->_type        = 'sqlsrv';
        $this->_server      = $server;
        $this->_database    = $database;
        $this->_username    = $username;
        $this->_password    = $password;
    }

    /**
     * Builds and returns an associative array of connection information for use with the internal PDO creation.
     *
     * array
     *      ['dsn']         string      The connection string to be used to open the PDO connection.
     *      ['username']    string      The username to use with the PDO connection.
     *      ['password']    string      The password to use with the PDO connection.
     *
     * @return string[string] The associative array of connection information. True if the username and password combination match, false if not.
     */
    protected final function getInfo() {
        $r = array(
            'username' => $this->_username,
            'password' => $this->_password,
            'dsn' => 
                                  $this->_type      . ':' 
                .   'Server='   . $this->_server    . ';'
                .   'Database=' . $this->_database
        );
        return $r;
    }

    // note: the commented section below are overrides which could allow us
    // to use the built in sqlsrv functions instead of the PDO methods.
    // this could be done to allow access to multiple returns from single statements
    // but would also require a large amount of code restructure, as the sqlsrv commands
    // do not throw errors by default, but instead require constant checks to sqlsrv_errors()

    // protected function conn() {
    //     // sql server decided to break the mold
    //     // and use their own silly functions
    //     // instead of using the main PDO stuff
    //     // override and use sql server specific items
    //     $conn = sqlsrv_connect(
    //         $this->_server
    //         , array(
    //             "Database" => $this->_database
    //         )
    //     );
    //     if ($conn === false) {
    //         // not sure if sqlsrv_errors() is a string format - probably not
    //         // if we use this, handle the error formatting better
    //         throw new \Exception(sqlsrv_errors()); 
    //     }
    //     return $conn;
    // }

    // private function getResult($resource) {
    //     $result = array();
    //     $i = 0;
    //     do {
    //         $result[$i] = array();
    //         // $result = array(); // we really only want the last result... is there a method to get that without traversing eeeeeverything?
    //         if (sqlsrv_has_rows($resource)) {
    //             while($row = sqlsrv_fetch_array($resource, \SQLSRV_FETCH_ASSOC)) {
    //                 $result[$i][] = $row;
    //                 // $result[] = $row;
    //             }
    //         }
    //         if (empty($result[$i])) {
    //             $result[$i] = null;
    //         }
    //         $i++;
    //     } while (sqlsrv_next_result($resource));
    //     return $result;
    // }

    // public function query($tsql, $params = array()) {
    //     $conn = $this->conn();
    //     try {
    //         if ($conn === false) {
    //             die(print_r(sqlsrv_errors(), true));
    //         }
    //         $resource = sqlsrv_query($conn, $tsql, $params);
    //         if ($resource === false) {
    //             die(print_r(sqlsrv_errors(), true));
    //         }
    //         $result = $this->getResult($resource);
    //     }
    //     catch (Exception $e) {
    //         $this->handle($e);
    //     }
    // }
}