<?php

class Version
{
    public $restler;
		
		// Versions of various applications and modules
		public $speedscreenVersion = '0.5.0';
		public $apiVersion = '1.1.10';
		public $apiLastUpdatedAt = '7/7/2014 10:52';

    function __construct(){
        header('Access-Control-Allow-Origin: *'); //Here for all /say
    }

    public function index($desiredData, $sub = null) {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        
        if ($desiredData == 'current')
        {
            return $this->current();
        }
        if ($desiredData == 'api')
        {
            return $this->api();
        }
        if ($desiredData == 'os')
        {
            return $this->os();
        }
    }

    public function current()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT TOP 1 * FROM Version_CS order by UpdatedDate desc";
        $tsql_params = array();

        $rows = $this->run_query($tsql, $tsql_params);

        $output = array();

        if(count($rows) == 0)
        {
            $_GET['suppress_response_codes'] = true;
            throw new RestException(412, 'No results returned.');
        }
        else
        {
            $output["CurrentVersion"] = $rows[0]["CurrentVersion"];
            $output["LastUpdated"] = $rows[0]["UpdatedDate"];
        }

        return $output;
    }

    public function api()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $output["CurrentVersion"] = $this->apiVersion;
        $output["LastUpdated"] = $this->apiLastUpdatedAt;
        return $output;
    }

    public function os()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $output["OS"] = php_uname('s');
        $output["Version"] = php_uname('v');
        return $output;
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