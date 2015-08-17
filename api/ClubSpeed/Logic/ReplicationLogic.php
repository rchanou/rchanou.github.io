<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert;

/**
 * The business logic class
 * for ClubSpeed replication.
 */
class ReplicationLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ReplicationLogic class.
     *
     * The ReplicationLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
    }

    /**
     * Checks to see if customer replication is enabled
     * by running a query on the ReplicateCustomerInfo
     * setting on dbo.ControlPanel.
     *
     * @return boolean True if replication is enabled, else false.
     */
    public function isReplicationEnabled() {
        $sql = "DECLARE @ReplicateCustomerInfo BIT;"
            ."\nSET @ReplicateCustomerInfo = "
            ."\n    CAST("
            ."\n        ("
            ."\n            SELECT COALESCE(cp.SettingValue, 'false')"
            ."\n            FROM dbo.ControlPanel cp "
            ."\n            WHERE cp.SettingName = 'ReplicateCustomerInfo'"
            ."\n        )"
            ."\n    AS BIT"
            ."\n)"
            ."\nSELECT @ReplicateCustomerInfo AS ReplicateCustomerInfo"
            ;
        $result = $this->db->query($sql);
        $ReplicateCustomerInfo = (bool)$result[0]['ReplicateCustomerInfo'];
        return $ReplicateCustomerInfo;
    }

    /**
     * Inserts replication logs for a specific customerId, table, and type.
     *
     * @param int       $customerId     The customer id for the existing, underlying customer account.
     * @param string    $table          The table which requires a new replication record.
     * @param string    $type           The type of replication change. Valid inputs are "Insert", "Update", and "Delete".
     *
     * @return void
     *
     * @throws InvalidArgumentException     if $customerId is not an integer.
     * @throws InvalidArgumentException     if $table is not a string.
     * @throws InvalidArgumentException     if $type is not a string.
     * @throws CustomerNotFoundException    if $customerId could not be found in the database.
     * @throws InvalidArgumentException     if $type is not one of "Insert", "Update", or "Delete".
     */
    public function insertReplicationLogs($customerId, $table, $type) {
        // any other variations of trigger logs?

        // should these be logic on TriggerLogsLogic? probably. consider moving in the future.
        if (!isset($customerId) || !is_int($customerId))
            throw new \InvalidArgumentException("Insert replication logs requires customerId to be an integer! Received: " . $customerId);
        if (!isset($table) || empty($table) || !is_string($table))
            throw new \InvalidArgumentException("Insert replication logs requires table to be a string! Received: " . $table);
        if (!isset($type) || empty($type) || !is_string($type))
            throw new \InvalidArgumentException("Insert replication logs requires type to be a string! Received: " . $type);
        if (!$this->logic->customers->customer_exists($customerId))
            throw new \CustomerNotFoundException("Insert replication logs could not find customerId in the database! Received: " . $customerId);
        
        $type = strtolower($type);
        $typeChar = $type[0];
        switch($typeChar) {
            case 'i':
                $actualType = 'Insert/Update';
                break;
            case 'u':
                $actualType = 'Insert/Update'; // inserts and updates are grouped together for replication
                break;
            case 'd':
                $actualType = 'Delete';
                break;
            default:
                throw new \InvalidArgumentException("Insert replication logs requires a valid type! Received: " . $type);
        }

        $this->logic->triggerlogs->create(array(
              'CustID'      => $customerId
            , 'LastUpdated' => Convert::getDate()
            , 'TableName'   => $table
            , 'Type'        => $actualType
        ));
    }
}