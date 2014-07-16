<?php

class Events
{
    public $restler;

    function __construct(){
        header('Access-Control-Allow-Origin: *'); //Here for all /say
    }


    protected function index($desiredData, $sub = null) {
        if ($desiredData == 'closures')
        {
            return $this->closures();
        }
    }

    protected function closures()
    {
        $tsql = "SELECT StartTime, EndTime FROM EventReservations WHERE StartTime > GETDATE() AND IsEventClosure = 1 ORDER BY StartTime";
        $tsql_params = array();

        $rows = $this->run_query($tsql, $tsql_params);

        if(count($rows) == 0)
        {
            $_GET['suppress_response_codes'] = true;
            throw new RestException(412, 'No closures found.');
        }

        return array('closures' => $rows);
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