<?php

namespace ClubSpeed\Database;
require_once(__DIR__.'./mssqlconnection.php');


/**
 * The extended MSSQL connection class containing default settings
 * for automatically connecting to a local instance of ClubSpeedV8.
 */
class CSConnection extends \ClubSpeed\Database\MSSQLConnection {

    /// note: this needs a restructure
    // our layers should (could?) be as follows:

    // 1. API
    // 2. Business Logic
    //    a. Database Layer (injected)
    //       1) Connection (injected)
    //       2) DbCollections
    //          a) DbRecords

    // /**
    //  * A reference to a instantiated CSBooking class,
    //  * which contains database interface methods for online booking.
    //  */
    // public $booking;

    // /**
    //  * A reference to a instantiated CSChecks class,
    //  * which contains database interface methods for checks.
    //  */
    // public $checks;

    // /**
    //  * A reference to a instantiated CSCustomers class,
    //  * which contains database interface methods for customers.
    //  */
    // public $customers;

    // /**
    //  * A reference to a instantiated CSEvents class,
    //  * which contains database interface methods for events.
    //  */
    // public $events;

    // /**
    //  * A reference to a instantiated CSFacebook class,
    //  * which contains database interface methods for facebook customers.
    //  */
    // public $facebook;

    // *
    //  * A reference to a instantiated CSHelpers class,
    //  * which contains database interface methods for helper functions.
     
    // public $helpers;

    // /**
    //  * A reference to a instantiated CSPasswords class,
    //  * which contains database interface methods for passwords.
    //  */
    // public $passwords;

    // /**
    //  * A reference to a instantiated CSReplication class,
    //  * which contains database interface methods for replication.
    //  */
    // public $replication;

    // /**
    //  * A reference to a instantiated CSReservations class,
    //  * which contains database interface methods for online booking reservations.
    //  */
    // public $reservations;

    // /**
    //  * A reference to a instantiated CSUsers class,
    //  * which contains database interface methods for users.
    //  */
    // public $users;

    /**
     * Creates a new instance of the CSConnection class.
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
        // $this->booking      = new \ClubSpeed\Database\CSBooking($this);
        // $this->checks       = new \ClubSpeed\Database\CSChecks($this);
        // $this->customers    = new \ClubSpeed\Database\CSCustomers($this);
        // $this->events       = new \ClubSpeed\Database\CSEvents($this);
        // $this->facebook     = new \ClubSpeed\Database\CSFacebook($this);
        // $this->helpers      = new \ClubSpeed\Database\CSHelpers($this);
        // $this->passwords    = new \ClubSpeed\Database\CSPasswords($this);
        // $this->replication  = new \ClubSpeed\Database\CSReplication($this);
        // $this->reservations = new \ClubSpeed\Database\CSReservations($this);
        // $this->users        = new \ClubSpeed\Database\CSUsers($this);
    }

    // public function insert($record) {
    //     $record->validate('insert');
    //     $insert = \ClubSpeed\Utility\Params::buildInsert($record);
    //     $lastId = $this->exec($insert['statement'], $insert['values']);
    //     return $lastId;
    // }

    // public function all($record) {
    //     $select = \ClubSpeed\Utility\Params::buildSelect($record);
    //     $results = $this->query($select['statement']);
    //     return $results;
    // }

    // public function get($record) {
    //     $get = \ClubSpeed\Utility\Params::buildGet($record);
    //     $results = $this->query($get['statement'], $get['values']);
    //     if (isset($results) && count($results) > 0)
    //         return $results[0];
    //     else
    //         return null; // do this?
    // }

    // public function find($record) {
    //     $where = \ClubSpeed\Utility\Params::buildFind($record);
    //     $results = $this->query($where['statement'], @$where['values']);
    //     return $results;
    // }

    // public function update($record) {
    //     $update = \ClubSpeed\Utility\Params::buildUpdate($record);
    //     $affected = $this->exec($update['statement'], @$update['values']);
    //     return $affected;
    // }

    // public function delete($record) {
    //     $delete = \ClubSpeed\Utility\Params::buildDelete($record);
    //     $affected = $this->exec($delete['statement'], $delete['values']);
    //     return $affected;
    // }
}
