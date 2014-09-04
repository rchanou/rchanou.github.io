<?php

class Logs
{
    public $restler;

    protected function index($desiredData, $sub = null) {
        switch($desiredData) {
            case 'getAfter':
                return $this->getAfter(@$_GET['id'], @$_GET['timestamp']);
                break;
        }
    }

    protected function getAfter($id, $timestamp)
    {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $output = array();
        
        if(!empty($id)) {
            $tsql = "SELECT TOP 2000 * FROM Logs WHERE LogID > ? ORDER BY LogID";
            $tsql_params = array(&$id);
        } elseif(!empty($timestamp)) {
            $tsql = "SELECT TOP 2000 * FROM Logs WHERE LogDate > ? ORDER BY LogID";
            $tsql_params = array(&$timestamp);                  
        }
        $rows = $this->run_query($tsql, $tsql_params);

        return array('logs' => $rows); 
    }

    private function run_query($tsql, $params = array()) {
        $tsql_original = $tsql . ' ';
                $logsDatabase = empty($GLOBALS['logsDatabase']) ? 'ClubSpeedLog' : $GLOBALS['logsDatabase'];
        // Connect
        try {
            $conn = new PDO( "sqlsrv:server=(local) ; Database=" . $logsDatabase, "", "");
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