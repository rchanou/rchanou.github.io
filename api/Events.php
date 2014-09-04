<?php

class Events
{
    public $restler;
    private $logic;

    function __construct(){
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = isset($GLOBALS['logic']) ? $GLOBALS['logic'] : null;
    }

    // events/list.json GET
    // note that list is a keyword in php -- careful with this...
    public function getlist($request_data) {
        if (!\ClubSpeed\Security\Validate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            $params = array(
                'FromDate'  => @$request_data['fromDate']
                , 'TrackNo' => @$request_data['trackId']
            );
            $fromDate = @$request_data['fromDate'];
            $trackId = @$request_data['trackId'];

            $results = $this->logic->events->getList($fromDate, $trackId);
            $events = array(
                'events' => array() // "namespace" the events
            );
            foreach($results as $result) {
                $events['events'][] = array(
                    "eventId"               => (int)$result['EventID']
                    , "eventDesc"           => $result['EventDesc']
                    , "eventDuration"       => (int)$result['EventDuration']
                    , "eventRoundNum"       => (int)$result['RoundNum']
                    , "eventScheduledTime"  => $result['EventScheduledTime']
                    , "eventTrackId"        => (int)$result['TrackNo']
                    , "eventType"           => $result['EventTypeName']
                );
            }
            return $events;
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    // todo: convert to the db abstracted class
    public function closures()
    {
        if (!\ClubSpeed\Security\Validate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT StartTime, EndTime FROM EventReservations WHERE StartTime > GETDATE() AND IsEventClosure = 1 AND Deleted = 0 ORDER BY StartTime";
        $tsql_params = array();

        $rows = $this->run_query($tsql, $tsql_params);

        if(count($rows) == 0)
        {
            $_GET['suppress_response_codes'] = true;
            throw new RestException(412, 'No closures found.');
        }

        return array('closures' => $rows);
    }

    // index needs to come AFTER any public functions for restler parsing purposes
    public function index($desiredData, $sub = null) {
        switch ($desiredData) {
            case 'closures':
                return $this->closures();
        }
    }

    private function run_query($tsql, $params = array()) {
        $tsql_original = $tsql . ' ';
        // Connect
        try {
            $conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            // Prepare statement
            $stmt = $conn->prepare($tsql);

            // Execute statement
            $stmt->execute($params);

            // Put in array
            $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            die('Exception Message:'  . $e->getMessage()  . '<br/>(Line: '. $e->getLine() . ')' . '<br/>Passed query: ' . $tsql_original . '<br/>Parameters passed: ' . print_r($params,true));
        }

        return $output;
    }
}