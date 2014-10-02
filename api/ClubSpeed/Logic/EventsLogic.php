<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed events.
 */
class EventsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the EventsLogic class.
     *
     * The EventsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        // to do: add an events interface
        // determine if there is even a single interface that this matches up against (note the EventHeatDetails call at the bottom)
    }

    /**
     * Validates the existence of an eventId in the database.
     * Note: This should be done by foreign keys, but since we don't have any
     *       for events, we must do our existence validation at some point.
     *
     * @param int $eventId The event id to check for existence.
     *
     * @return boolean If the eventId is found in dbo.Events then true, else false.
     *
     * @throws InvalidArgumentException If the eventId provided is either not set or a non-integer.
     */
    public final function event_exists($eventId) {
        if (!isset($eventId) || !is_int($eventId))
            throw new \InvalidArgumentException("Event exists requires eventId to be an integer! Received: $eventId");

        $sql = "SELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT e.*"
            ."\n        FROM dbo.EVENTS e"
            ."\n        WHERE e.EventID = ?"
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS EventExists";
        $params = array($eventId);
        $results = $this->db->query($sql, $params);
        $eventExists = \ClubSpeed\Utility\Convert::toBoolean($results[0]['EventExists']);
        return $eventExists;
    }

    /**
     * Collects a list of available events from either a given date (when provided)
     * or today's date (when not provided) on either a specific track (when provided)
     * or for all tracks available.
     *
     * @param string    $fromDate (optional) The date from which to begin the event search.
     * @param int       $trackId  (optional) The specific track from which to search for events.
     *
     * @return array[] An array containing event information.
     */
    public final function getList($fromDate, $trackId) {
        $params = array();

        if (!isset($fromDate))
            $fromDate = date('Y-m-d'); // FromDate not set -- get today's date
        $fromDate = \ClubSpeed\Utility\Convert::toDateForServer($fromDate);
        $params[] = $fromDate; // push on to paramsValues in order the sql statement expects

        // sql for getting only the date (works on all versions of sql)
        // SELECT DATEADD(DAY, 0, DATEDIFF(DAY, 0, GETDATE())) -- Get current date without time
        $sql = "SELECT"
            ."\n    e.EventID"
            ."\n    , e.EventTheme"
            ."\n    , e.EventDesc"
            ."\n    , e.EventTypeName"
            ."\n    , e.EventDuration"
            ."\n    , e.EventScheduledTime"
            ."\n    , e.RoundNum"
            ."\n    , e.TrackNo"
            ."\nFROM EVENTS e"
            ."\nWHERE"
            ."\n    DATEADD(DAY, 0, DATEDIFF(DAY, 0, e.EventScheduledTime)) >= DATEADD(DAY, 0, DATEDIFF(DAY, 0, ?))" // get dates without times -- SQL compatibility pre-2008 method
            ;
        if (isset($trackId) && is_numeric($trackId)) {
            // if trackId is included, then append to the sql statement's where clause and push to params
            $sql .= "\n    AND e.TrackNo = ?";
            $params[] = $trackId; // push on to values in order the sql statement expects
        }
        $sql = $sql
            ."\nORDER BY"
            ."\n    e.EventScheduledTime";

        $events = $this->db->query($sql, $params);
        return $events;
    }

    /**
     * Collects a list of available events from either a given date (when provided)
     * or today's date (when not provided) on either a specific track (when provided)
     * or for all tracks available.
     *
     * @param string    $eventId            The id for the event queue to which to add the customer. 
     * @param int       $customerId         The id for the customer to add to the event queue.
     * @param int       $checkId (optional) The id for the check to store with the event id and customer id combination.
     *
     * @return array[] An array containing event information.
     *
     * @throws InvalidArgumentException     If eventId is either not set or a non-integer.
     * @throws InvalidArgumentException     If customerId is either not set or non-numeric.
     * @throws EventNotFoundException       If eventId could not be found in the database.
     * @throws CustomerNotFoundException    If customerId could not be found in the database.
     */
    public final function add_to_queue($eventId, $customerId, $checkId = 0) {

        // see dbo.AddToEventQueues and \WinForm\MainEngine\Services\QueuesService.vb.AddToEventQueues()

        if (!isset($eventId) || !is_int($eventId))
            throw new \InvalidArgumentException("Event add to queue requires numeric eventId! Received: $eventId");
        if (!isset($customerId) || !is_int($customerId))
            throw new \InvalidArgumentException("Event add to queue requires numeric customerId! Received: $customerId");
        if (!$this->event_exists($eventId))
            throw new \EventNotFoundException("Event add to queue was unable to find event in the database! Received eventId: $eventId");
        if (!$this->logic->customers->customer_exists($customerId))
            throw new \CustomerNotFoundException("Event add to queue was unable to find customer in the database! Received customerId: $customerId");

        $sql = "DECLARE @EventID INT; SET @EventID = ?"
            ."\nDECLARE @CustID  INT; SET @CustID  = ?"
            ."\nDECLARE @CheckID INT; SET @CheckID = ?"
            ."\nIF NOT EXISTS("
            ."\n    SELECT ehd.CustID"
            ."\n    FROM dbo.EventHeatDetails ehd"
            ."\n    WHERE"
            ."\n            ehd.CustID  = @CustID"
            ."\n        AND ehd.EventID = @EventID"
            ."\n)"
            ."\nBEGIN"
            ."\n    INSERT INTO dbo.EventHeatDetails("
            ."\n        EventID"
            ."\n        , CustID"
            ."\n        , DateAdded"
            ."\n        , RoundLoseNum"
            ."\n        , CheckID"
            ."\n        , RPM"
            ."\n    )"
            ."\n    VALUES ("
            ."\n        @EventID"
            ."\n        , @CustID"
            ."\n        , GETDATE()" // DateAdded
            ."\n        , 99" // RoundLoseNum - VB is setting to 99 by default
            ."\n        , @CheckID"
            ."\n        , COALESCE((SELECT MAX(c.RPM) FROM dbo.CUSTOMERS c WHERE c.CustID = @CustID), 1200)" // using a default of 1200, since RPM in this table does not allow nulls
            ."\n    );"
                    // note that we can't actually grab the following select statement
                    // without using a second separate query, 
                    // unless we use sqlsrv specific functions instead of PDO calls
            ."\n    SELECT ISNULL(e.TotalRacers, 8) AS TotalRacers" 
            ."\n    FROM dbo.Events e"
            ."\n    WHERE e.EventID = @EventID"
            ."\nEND"
            ."\nELSE"
            ."\nBEGIN"
            ."\n    SELECT 0 AS TotalRacers"
            ."\nEND"
            ;
        $params = array(
              $eventId
            , $customerId
            , $checkId
        );
        $this->db->exec($sql, $params);
        $GLOBALS['webapi']->clearCache();
    }
}