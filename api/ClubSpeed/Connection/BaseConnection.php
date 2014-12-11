<?php

namespace ClubSpeed\Connection;

/**
 * The base class designed to connect to
 * and interface with a database.
 */
abstract class BaseConnection {

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
    abstract protected function getInfo();

    /**
     * Built-in error handling class.
     *
     * Note that the default error handling is to simply re-throw the error.
     * If custom error handling for internal database queries and executions is desired,
     * this method should be overridden in the extended class.
     *
     * @param Exception $e The exception to handle.
     * @return void
     * @throws Exception by default.
     */
    protected final function handle($e) {
        throw $e; // just re-throwing for now
    }

    /**
     * Builds a PDO connection object using the information collected by $this->getInfo.
     *
     * @see $this->getInfo() To collect the information used by conn().
     * @return PDO The PDO connection.
     * @throws Exception if the connection was unable to be created.
     */
    protected function conn() {
        try {
            // param1 = DSN / connectionString
            // param2 = username
            // param3 = password
            // param4 = array of options
            $info = $this->getInfo();
            $conn = new \PDO(
                $info['dsn']
                , $info['username']
                , $info['password']
            );
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch (Exception $e) {
            $this->handle($e);
        }
    }

    /**
     * Executes a SQL statement wrapped in a transaction, allowing for automated rollback on exceptions.
     *
     * Huge side note: this does NOT WORK with the silly SQLSRV driver for PDO. 
     * If we want this sort of protection, we will need to override in MSSQLConnection.
     *
     * @param string    $tsql                           The statement to execute.
     * @param string    $params             (optional)  The array of arguments to use with the prepared statement. Set to an empty set of parameters by default.
     * @param string    $expectedToAffect   (optional)  The number of rows expected to be affected. Set to 1 by default. Designed to be used as a failsafe against accidental multiple deletion or updates.
     *
     * @return void
     */
    public function trans($tsql, $params = array(), $expectedToAffect = 1) {
        $conn = $this->conn();
        $conn->beginTransaction();
        try {
            $affected = $this->exec($tsql, $params);
            if ($affected === $expectedToAffect) {
                $conn->commit();
            }
            else {
                $conn->rollBack();
            }
            return $affected;
        }
        catch (Exception $e) {
            $conn->rollBack();
            $this->handle($e);
        }
    }

    /**
     * Executes a SQL statement.
     *
     * @param string    $tsql               The statement to execute.
     * @param string    $params (optional)  The array of arguments to use with the prepared statement. Set to an empty set of parameters by default.
     *
     * @return void
     */
    public function exec($tsql, $params = array()) {
        $conn = $this->conn();
        try {
            $stmt = $conn->prepare($tsql);
            $stmt->execute($params);
            if (stripos($tsql, 'INSERT') !== false) {
                $return = $conn->lastInsertId();
            }
            else {
                $return = $stmt->rowCount();
            }
            return $return;
        }
        catch (Exception $e) {
            $this->handle($e);
        }
    }

    /**
     * Runs a SQL query.
     *
     * @param string    $tsql               The query to execute.
     * @param string    $params (optional)  The array of arguments to use with the prepared statement. Set to an empty set of parameters by default.
     *
     * @return mixed[int] The array of associative arrays representing the return from the PDO query.
     */
    public function query($tsql, $params = array()) {
        $conn = $this->conn();
        try {
            $stmt = $conn->prepare($tsql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC); // will return as an array instead of a single-use lazy loading enumerable
            // // NOTE! the following yield structure would require PHP 5.5
            // // use the global "from" function to convert generator to enumerable?
            // return \zutil\from(function() use ($stmt) {
            //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //         yield $row;
            //     }
            // });
        }
        catch (Exception $e) {
            $this->handle($e);
        }
    }
}