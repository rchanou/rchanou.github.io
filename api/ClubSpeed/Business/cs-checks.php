<?php

namespace ClubSpeed\Business;

/**
 * The business logic class
 * for ClubSpeed checks.
 */
class CSChecks {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSChecks class.
     *
     * The CSChecks constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
    }

    /**
     * Document: TODO
     */
    // public final function create($params = array()) {
    //     $params = \ClubSpeed\Utility\Params::nonReservedData($params);
    //     $mapped = $this->db->resourceSets->map('server', $params);
    //     $record = $this->db->resourceSets->dummy();
    //     $record->load($mapped);
    //     $record->validate('insert'); // validate early before attempting the find with potentially missing columns?
    //     $find = $this->db->resourceSets->find(array(
    //         'ResourceSetName'   => $record->ResourceSetName
    //         , 'ResourceName'    => $record->ResourceName
    //         , 'Culture'         => $record->Culture
    //     ));
    //     if (!empty($find))
    //         throw new \RecordAlreadyExistsException('Translation create found a record which already exists! Received ResourceSetName: ' . $mapped['ResourceSetName'] . ' and Resourcename: ' . $mapped['ResourceName']);
    //     $resourceId = $this->db->resourceSets->create($mapped);
    //     $create = $this->db->resourceSets->map(
    //         'client'
    //         , array(
    //             $this->db->resourceSets->key => $resourceId
    //         )
    //     );
    //     return $create;
    // }


    /**
     * Creates a new check in the database.
     *
     * @param int $userId The user id for the user who is creating the check.
     * @param int $customerId The customer id to place on the check.
     * @param string $checkName (optional) The name of the check.
     * @param string $brokerName (optional) The name of the broker.
     * @param string $notes (optional) The notes for the check.
     *
     * @return boolean If the customerId is found in dbo.Customers then true, else false.
     *
     * @throws InvalidArgumentException     If the userId parameter is not an integer.
     * @throws InvalidArgumentException     If the customerId parameter is not an integer.
     * @throws UserNotFoundException        If the userId could not be found in the database.
     * @throws CustomerNotFoundException    If the customerId could not be found in the database.
     */
    public final function create($params = array()) {

        return; // break early for commit to live branch

        // $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        // $mapped = $this->db->checks->map('server', $params);
        // $dummy = $this->db->checks->dummy();
        // $dummy->load($mapped);
        // $dummy->validate('insert');
        // $dummy->CheckStatus = $dummy->CheckStatus ?: 0;
        // $dummy->OpenedDate = \ClubSpeed\Utility\Convert::toDateForServer(date(\ClubSpeed\Utility\Convert::DATE_FORMAT_FROM_CLIENT, time()), 'Y-m-d H:i:s');
        // $create = $this->db->checks->create($dummy);
        // pr($create);
        // die();

        // if (!$this->cs->users->user_exists($userId))
        //     throw new \UserNotFoundException("Check create could not find userId in the database! Received: $userId");
        // if (!$this->cs->customers->customer_exists($customer))
        //     throw new \CustomerNotFoundException("Check create could not find customerId in the database! Received: $customerId");

        // $sql = "DECLARE @UserID     INT;            SET @UserID     = ?;"
        //     ."\nDECLARE @CustID     INT;            SET @CustID     = ?;"
        //     ."\nDECLARE @CheckName  NVARCHAR(255);  SET @CheckName  = ?;"
        //     ."\nDECLARE @BrokerName NVARCHAR(255);  SET @BrokerName = ?;"
        //     ."\nDECLARE @Notes      NVARCHAR(255);  SET @Notes      = ?;"
        //     ."\nBEGIN"
        //     ."\n    INSERT INTO dbo.CHECKS ("
        //     ."\n        CheckType"
        //     ."\n        , OpenedDate"
        //     ."\n        , CheckStatus"
        //     ."\n        , UserID"
        //     ."\n        , CustID"
        //     ."\n        , CheckName"
        //     ."\n        , BrokerName"
        //     ."\n        , Notes"
        //     ."\n    )"
        //     ."\n    SELECT"
        //     ."\n        1 -- CheckType.Regular"
        //     ."\n        , GETDATE() -- VB Now"
        //     ."\n        , 0 -- CheckStatus.OPEN"
        //     ."\n        , @UserID"
        //     ."\n        , @CustID"
        //     ."\n        , @CheckName"
        //     ."\n        , @BrokerName"
        //     ."\n        , @Notes;"
        //     // ."\n    SELECT @@IDENTITY;" // this does NOT work, unless we convert to using custom sqlsrv() functions -- see http://php.net/manual/en/book.sqlsrv.php
        //     ."\nEND"
        //     ;

        // $params = array(
        //     $userId
        //     , $customerId
        //     , $checkName
        //     , $brokerName
        //     , $notes
        // );

        // $result = $this->cs->query($sql, $params); // result will be lastInsertedId IFF the Checks table has a single ID which is IDENTITY NOT NULL
        // return $result;
    }

    private function validate($type, $params) {
        switch ($type) {
            case 'insert':
                if (is_null($this->UserID) || !is_int($this->UserID))
                    throw new \RequiredArgumentMissingException("Check create requires UserID to be an integer! Received: " . $this->UserID);
                if (is_null($this->CustID) || !is_int($this->CustID))
                    throw new \RequiredArgumentMissingException("Check create requires CustID to be an integer! Received: " . $this->CustID);
                break;
            case 'update':

                break;
            case 'delete':

                break;
        }
    }
}