<?php

namespace ClubSpeed\Connection;
require_once(__DIR__.'./MSSQLConnection.php');

/**
 * The extended MSSQL connection class containing default settings
 * for automatically connecting to a local instance of ClubSpeedV8.
 */
class ClubSpeedConnection extends \ClubSpeed\Connection\MSSQLConnection {

    /**
     * Creates a new instance of the ConnectionClubSpeed class.
     *
     * @param string    $server   (optional)    The server to which to connect. If none is provided, then (local) is used.
     * @param string    $database (optional)    The database instance to use as a default. If none is provided, then ClubSpeedV8 is used.
     * @param string    $username (optional)    The username to use for credentials. If not provided, then integrated windows authentication will be used.
     * @param string    $password (optional)    The password to use for credentials. If not provided, then integrated windows authentication will be used.
     */
    public function __construct(
          $server   = '(local)'
        , $database = 'ClubSpeedV8'
        , $username = ""
        , $password = ""
    ) {
        parent::__construct($server, $database, $username, $password);
    }
}
