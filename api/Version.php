<?php

class Version
{
    public $restler;
    private $logic;
		
		// Versions of various applications and modules
		public $speedscreenVersion = '2.0.0';
		public $apiVersion = '1.6';
		public $apiLastUpdatedAt = '3/16/2016 10:00';

    function __construct(){
        $this->logic = $GLOBALS['logic'];
        // header('Access-Control-Allow-Origin: *'); //Here for all /say
    }

    public function index($desiredData, $sub = null) {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        
        switch($desiredData) {
            case "current":
                return $this->current();
                break;
            case "api":
                return $this->api();
                break;
            case "os":
                return $this->os();
                break;
            case "sql":
                return $this->sql();
                break;
            case "eurekas":
                return $this->eurekas();
            default:
                throw new RestException(401, "Invalid version parameter!");
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
    
    public function sql()
    {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT @@version";
        $tsql_params = array();

        $rows = $this->run_query($tsql, $tsql_params);
        
        $output = count($rows) > 0 ? $rows[0][''] : null;
        return array('SqlVersion' => $output);
    }

    public function eurekas() {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) { // or private access?
            throw new RestException(401, "Invalid authorization!");
        }
        if (!$this->logic->version->hasEurekas())
            throw new RestException(404);
        return;
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