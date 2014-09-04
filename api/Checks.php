<?php

class Checks
{
    public $restler;
    private $db;
    
    function __construct(){
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->db = $GLOBALS['db'];
    }

    // convert to postcreate after testing has completed
    public function postcreate($request_data) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        $params = array(
            'UserID'        => @$request_data['userId']
            , 'CustID'      => @$request_data['customerId']
            , 'CheckName'   => @$request_data['checkName']
            , 'BrokerName'  => @$request_data['brokerName']
            , 'Notes'       => @$request_data['notes']
        );
        $results = $this->db->checks->create($params);
        return $results;
    }

    public function index($method, $sub = null) {
        switch($method) {
            case 'accounting_report':
                if($_REQUEST['key'] != $GLOBALS['privateKey'])
                    throw new RestException(412,'Not authorized');
                return $this->accounting_report(@$_REQUEST['start'], @$_REQUEST['end']);
            // case 'create':
            //     pr("hit index create");
            //     return $this->create($_REQUEST);
        }
        if(!is_numeric($check_id)) throw new RestException(412,'Not a valid check id');
    }

    

    // /**
    //  * Get a check
    //  * @protected
    //  * @param integer $check_id
    //  * @return array 
    //  */
    // protected function check($check_id) {
    //     if(!is_numeric($check_id)) throw new RestException(412,'Not a valid check id');

    //     // Get check
    //     $result = $this->run_query("SELECT * FROM Checks WHERE CheckId = ?", array(&$check_id));
    //     $check = $result[0];

    //     $check['customer']  = $this->run_query('SELECT * FROM Customers WHERE CustID = ?', array(&$check['CustID']));
    //     $check['lineItems'] = $this->run_query('SELECT * FROM CheckDetails WHERE CheckID = ?', array(&$check_id));
    //     $check['payment']   = $this->run_query('SELECT * FROM Payment WHERE CheckID = ?', array(&$check_id));

    //     return array('check' => $check);    
    // }
    
    // protected function accounting_report($start = null, $end = null) {
    //     $start = empty($start) ? date($GLOBALS['dateFormat']) : $start;
    //     $start = date($GLOBALS['dateFormat'] . " H:i:s", strtotime($start));
    //     $end = empty($end) ? $start : $end;
    //     $end = date($GLOBALS['dateFormat'] . " H:i:s", strtotime(date($GLOBALS['dateFormat'], strtotime($end))) + 24*60*60); // End of today
            
    //     $result = $this->run_query("GetAccoutingReport '$start', '$end'"); // Not using params because we are sanitizing above and this breaks SQL Express for some reason...

    //     $output = array();

    //     foreach($result as $row) {
    //         $row['Debit'] = $row['Debit'] == '.00' ? null : $row['Debit'];
    //         $row['Credit'] = $row['Credit'] == '.00' ? null : $row['Credit'];
    //         $output[$row['Description']] = array('debit' => $row['Debit'], 'credit' => $row['Credit']);
    //     }

    //     return array('report' => $output);  
    // }
    
    
    
    // private function run_query($tsql, $params = array()) {
        
    //     // Connect
    //     try {
    //         $conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
    //         $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            
    //         // Prepare statement
    //         $stmt = $conn->prepare($tsql);
    
    //         // Execute statement
    //         $stmt->execute($params);
            
    //         // Put in array
    //         $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     } catch(Exception $e) { 
    //         die( print_r( $e->getMessage() ) ); 
    //     }
        
    //     return $output;
    // }
    
}