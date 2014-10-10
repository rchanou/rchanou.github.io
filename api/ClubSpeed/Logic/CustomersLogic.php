<?php

namespace ClubSpeed\Logic;
require_once(__DIR__.'/../../Settings.php');
require_once(__DIR__.'/../../Queues.php');

/**
 * The business logic class
 * for ClubSpeed customers.
 */
class CustomersLogic extends BaseLogic {

    /**
     * Constructs a new instance of the CustomersLogic class.
     *
     * The CustomersLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    private $settings;

    /**
     * A static (const) list of countries which legally disallow
     * requiring an email on user registration.
     *
     * Note: Store these as their lowercase counterparts for string comparison.
     */
    private static $countriesWhichCannotRequireEmail = array(
        "Canada"
    );

    /**
     * Constructs a new instance of the CSCustomers class.
     *
     * The CSCustomers constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->settings = new \Settings();
        $this->interface = $this->db->customers;
    }

    /**
     * Collects the next available customer id for the given location.
     *
     * Note that this  method is used instead of using the database for auto incrementing,
     * since the customer id range is defined by the LocationID found in the ControlPanel.
     *
     * Also note, this method could technically result in a collision,
     * but has not had any problems yet through the testing or live phases.
     *
     * @return int The next available customer id for the current location.
     */
    private function getNextCustId() {
        $sql = array(
              "DECLARE @LocationID INT;"
            , "SET @LocationID = CAST("
            , "    ("
            , "        SELECT TOP 1 cp.SettingValue "
            , "        FROM dbo.ControlPanel cp "
            , "        WHERE cp.SettingName = 'LocationID'"
            , "    ) AS INT"
            , ")"
            , "DECLARE @CustIDLowerRange INT;"
            , "SET @CustIDLowerRange = (@LocationID * 1000000) + 1"
            , "DECLARE @CustIDUpperRange INT;"
            , "SET @CustIDUpperRange = (1 + @LocationID) * 1000000"
            , "DECLARE @CustID INT;"
            , "SET @CustID = ("
            , "    SELECT ISNULL(MAX(c.CustID)+1, @CustIDLowerRange)"
            , "    FROM CUSTOMERS c"
            , "    WHERE c.CustID BETWEEN @CustIDLowerRange AND @CustIDUpperRange"
            , ")"
            , "SELECT @CustID AS CustID"
        );
        $sql = implode("\n", $sql);
        $result = $this->db->query($sql);
        $CustID = (int)$result[0]['CustID'];
        return $CustID;
    }

    /**
     * Validates the existence of an customerId in the database.
     * Note: This should be done by foreign keys, but since we don't have any
     *       for customers, we must do our existence validation at some point.
     *
     * @param int $customerId The customer id to check for existence.
     * @return boolean If the customerId is found in dbo.Customers then true, else false.
     * @throws InvalidArgumentException If the customerId parameter is not set or a non-integer.
     */
    public final function customer_exists($customerId) {
        if (!isset($customerId) || !is_int($customerId))
            throw new \InvalidArgumentException("Customer exists requires customerId to be an integer! Received: $customerId");

        $sql = "SELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT c.*"
            ."\n        FROM dbo.CUSTOMERS c"
            ."\n        WHERE c.CustID = ?"
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS CustomerExists";
        $params = array($customerId);
        $results = $this->db->query($sql, $params);
        $customerExists = \ClubSpeed\Utility\Convert::toBoolean($results[0]['CustomerExists']);
        return $customerExists;
    }

    /**
     * Attempts to log in a customer using the provided email address and password.
     *
     * @param string $email The email for the login
     * @param string $password The password for the login.
     *
     * @return int[string] An array containing the customer id located at ['CustID'].
     *
     * @throws InvalidArgumentException If the email provided is not a string.
     * @throws InvalidArgumentException If the the password provided is not a string.
     * @throws InvalidEmailException    If the email provided could not be found in the database.
     * @throws InvalidPasswordException If the provided password does not match the hash stored in the database.
     */
    public final function login($email, $password) {
        if (!isset($email) || !is_string($email))
            throw new \InvalidArgumentException("Customer login requires email to be a string! Received: $email");
        if (!isset($password) || !is_string($password))
            throw new \InvalidArgumentException("Customer login requires password to be a string!");

        $account = $this->find_primary_account($email);
        if(empty($account)) {
            // Customer email could not be found in the database
            throw new \InvalidEmailException("Invalid credentials!");
        }

        if (!\ClubSpeed\Security\Hasher::verify($password, $account['Password'])) {
            // note that $account['Password'] is a salted hash, not the actual password
            // Hasher::verify will handle the salted hash comparison to the provided password
            throw new \InvalidPasswordException("Invalid credentials!");
        }
        return array(
            "customerId" => (int)$account['CustID'] // PDO returns CustID as a string
            , "firstName" => $account['FName'] // for testing purposes
        );
    }

    /**
     * Searches for a primary account for a given email address.
     *
     * @param string $email The email for which to search.
     * @return mixed[string] An associative array containing primary account information.
     * @throws InvalidArgumentException If the email provided is not a string.
     */
    public final function find_primary_account($email) {
        if (!isset($email) || !is_string($email))
            throw new \InvalidArgumentException("find_primary_account requires email to be a string! Received: " . $email);

        $sql = "SELECT"
            ."\n    c.CustID"
            ."\n    , ISNULL(c.EmailAddress, '') AS EmailAddress"
            ."\n    , c.Password"
            ."\n    , ISNULL(p.Points, 0) AS Points"
            ."\n    , c.TotalRaces"
            ."\n    , c.LastVisited"
            ."\n    , c.RPM AS ProSkill"
            ."\n    , c.FName"
            ."\n    , c.LName"
            ."\nFROM CUSTOMERS c"
            ."\nLEFT OUTER JOIN ("
            ."\n    SELECT p.CustID, SUM(ISNULL(p.PointAmount, 0)) as Points"
            ."\n    FROM POINTHISTORY p"
            ."\n    WHERE"
            ."\n        p.PointExpDate IS NULL"
            ."\n        OR p.PointExpDate >= GETDATE()"
            ."\n    GROUP BY p.CustID"
            ."\n) AS p ON p.CustID = c.CustID"
            ."\nWHERE"
            ."\n        c.EmailAddress = ?"
            ."\n    AND c.Deleted = 0"
            ."\nORDER BY"
            ."\n    CASE WHEN c.Password IS NULL THEN 1 ELSE 0 END" // push null passwords to bottom
            ."\n    , Points DESC"
            ."\n    , c.TotalRaces DESC"
            ."\n    , c.LastVisited DESC"
            ."\n    , c.RPM DESC"
            ;
        $params = array($email);
        $results = $this->db->query($sql, $params);
        if (count($results) > 0) {
            return $results[0];
        }
        return array(); // return empty array, or throw exception?
        // throw UnexpectedValueException("find_primary_account was unable to find any accounts with EmailAddress of: " . $email);
    }

    /**
     * Checks whether or not a provided email exists in the database.
     *
     * @param string $email The email for which to search.
     * @return boolean True if the email is found in the database, false if not.
     * @throws InvalidArgumentException If the email provided is not a string.
     */
    public final function email_exists($email) {
        if (!isset($email) || !is_string($email)) 
            throw new \InvalidArgumentException("Email existence check requires $email to be a string!");

        $sql = array(
            "SELECT COUNT(*) AS [Count]"
            , "FROM CUSTOMERS c"
            , "WHERE c.EmailAddress=?"
            , "AND c.Deleted = 0"
        );
        $sql = implode("\n", $sql);
        $params = array($email);
        $results = $this->db->query($sql, $params);
        $count = $results[0]['Count']; // results should always contain 1 row (no more, no less)
        return ($count > 0);
    }

    public final function email_is_claimed($email) {
        if (!isset($email) || !is_string($email)) 
            throw new \InvalidArgumentException("Email existence check requires $email to be a string!");

        $sql = array(
            "SELECT COUNT(*) AS [Count]"
            , "FROM CUSTOMERS c"
            , "WHERE c.EmailAddress=?"
            , "AND c.Password IS NOT NULL"
            , "AND c.Deleted = 0"
        );
        $sql = implode("\n", $sql);
        $params = array($email);
        $results = $this->db->query($sql, $params);
        $count = $results[0]['Count']; // results should always contain 1 row (no more, no less)
        return ($count > 0);
    }

    /**
     * Deletes a customer from the database by a provided customer id.
     *
     * @param int $customerId The id of the customer to delete.
     * @return int The quantity of deleted customers.
     * @throws InvalidArgumentException If the customer id provided is not an integer.
     */
    public final function delete($customerId) {
        if (!isset($customerId) || !is_int($customerId))
            throw new \InvalidArgumentException("Customer delete requires customerId to be an integer! Received: $customerId");

        $sql = array(
            "DELETE c"
            , "FROM CUSTOMERS c"
            , "WHERE c.CustID = ?"
        );
        $sql = implode("\n", $sql);
        $params = array($customerId);
        $affected = $this->db->trans($sql, $params); // expected to delete 1 record
        return $affected;
    }

    /**
     * Gets all settings from the Settings class as an associative key=>value array.
     *
     * @private
     * @return mixed[string] The key=>value array of settings mixed sorted into namespaces by terminal name.
     */
    private function getSettings() {
        // grab and build the settings
        // into an easily accessible interface
        // for our purposes (chopping out extra info)
        $kiosk = $this->settings->getSettings('kiosk');
        $settings = array();
        foreach($kiosk['settings'] as $setting) {
            if (!isset($settings[$setting['TerminalName']]))
                $settings[$setting['TerminalName']] = array();
            $settings[$setting['TerminalName']][$setting['SettingName']] = $setting['SettingValue'];
        }
        return $settings;
    }

    /**
     * Determines which columns are required and allowed for a customer create call
     * by looking up the list of kiosk settings through the Settings class.
     *
     * @param mixed[string] $params (optional) The array of existing params for a create call.
     *
     * @return string[string]string The array containing both required and allowed key=>value arrays.
     */
    private function checkControlPanelForRequiredAndAllowedColumns(&$params = array()) {

        //----------------
        // Business logic
        //----------------
        $settings = $this->getSettings();
        $settings = $settings['kiosk']; // collect kiosk settings
        $columns = array(
            'required' => array()
            , 'allowed' => array()
        );
        $required = &$columns['required'];
        $allowed = &$columns['allowed'];

        // predefined, hardcoded assumptions (non-editable by the admin panels or config.php)
        $required[] = "FName";
        $required[] = "LName";
        $required[] = "BirthDate";
        $required[] = "Gender";

        $allowed[] = "Privacy4"; // for facebook settings
        $allowed[] = "TotalVisits"; // for default settings
        $allowed[] = "Password";

        if      ($settings['CfgRegAddReq'])        {$required[] = 'Address'; $allowed[] = 'Address2';} // assume Address2 is allowed when Address is required
        else if ($settings['CfgRegAddShow'])       {$allowed[]  = 'Address'; $allowed[] = 'Address2';} // assume Address2 is allowed when Address is allowed

        if      ($settings['CfgRegCityReq'])        $required[] = 'City';
        else if ($settings['CfgRegCityShow'])       $allowed[]  = 'City';

        if      ($settings['CfgRegCntryReq'])       $required[] = 'Country';
        else if ($settings['CfgRegCntryShow'])      $allowed[]  = 'Country';

        if      ($settings['CfgRegDrvrLicReq'])     $required[] = 'LicenseNumber';
        else if ($settings['CfgRegDrvrLicShow'])    $allowed[]  = 'LicenseNumber';

        if ($settings['CfgRegEmailReq']) {
            if (isset($params['Country']) && !empty($params['Country'])) {
                $country = $params['Country'];
                // using an anonymous function instead of in_array for case-insensitive search
                $overrideEmailReq = call_user_func(function($country, $overrideCountries) {
                    foreach($overrideCountries as $overrideCountry) {
                        if (strcasecmp($country, $overrideCountry) === 0) {
                            return true;
                        }
                    }
                    return false;
                }, $country, self::$countriesWhichCannotRequireEmail);
                if ($overrideEmailReq === true) {
                    $allowed[] = 'EmailAddress';
                }
                else {
                    $required[] = 'EmailAddress';
                }
                $allowed[] = 'DoNotMail';
            }
            else {
                $required[] = 'EmailAddress';
                $allowed[] = 'DoNotMail';
            }
        }
        else if ($settings['CfgRegEmailShow']) {
            $allowed[] = 'EmailAddress';
            $allowed[] = 'DoNotMail';
        }

        // if      ($settings['CfgRegEmailReq'])      { isset($params['Country']) && strtolower($params['Country'] === 'canada') ? $allowed[] = 'EmailAddress' : $required = $required[] = 'EmailAddress'; $allowed[] = 'DoNotMail';}
        // else if ($settings['CfgRegEmailShow'])     {$allowed[]  = 'EmailAddress'; $allowed[] = 'DoNotMail';}

        if      ($settings['CfgRegPhoneReq'])       $required[] = 'Cell';
        else if ($settings['CfgRegPhoneShow'])      $allowed[]  = 'Cell';

        if      ($settings['CfgRegRcrNameReq'])     $required[] = 'RacerName';
        else if ($settings['CfgRegRcrNameShow'])    $allowed[]  = 'RacerName';

        if      ($settings['CfgRegSrcReq'])         $required[] = 'SourceID';
        else if ($settings['CfgRegSrcShow'])        $allowed[]  = 'SourceID';

        if      ($settings['CfgRegStateReq'])       $required[] = 'State';
        else if ($settings['CfgRegStateShow'])      $allowed[]  = 'State';

        if      ($settings['CfgRegZipReq'])         $required[] = 'Zip';
        else if ($settings['CfgRegZipShow'])        $allowed[]  = 'Zip';

        if      ($settings['cfgRegCustTxt1req'])    $required[] = 'Custom1';
        else if ($settings['cfgRegCustTxt1Show'])   $allowed[]  = 'Custom1';

        if      ($settings['cfgRegCustTxt2req'])    $required[] = 'Custom2';
        else if ($settings['cfgRegCustTxt2Show'])   $allowed[]  = 'Custom2';

        if      ($settings['cfgRegCustTxt3req'])    $required[] = 'Custom3';
        else if ($settings['cfgRegCustTxt3Show'])   $allowed[]  = 'Custom3';

        if      ($settings['cfgRegCustTxt4req'])    $required[] = 'Custom4';
        else if ($settings['cfgRegCustTxt4Show'])   $allowed[]  = 'Custom4';

        return $columns;
    }

    /**
     * Creates a Customer account using the provided information.
     * The list of required information and allowed information
     * is driven by the list of required and shown items provided by
     * the control panel settings (see checkControlPanelForRequiredAndAllowedColumns()).
     *
     * @param mixed[string] $params An array of parameters used for creating a new customer record.
     * 
     * @return int[string] An array containing the new customerId at ['CustID'].
     *
     * @throws RequiredParameterMissingException if any of the required parameters are either not set or empty in $params.
     */
    public final function create($params = array()) {

        //----------------
        // Business logic
        //----------------

        // $columns = $this->checkControlPanelForRequiredAndAllowedColumns($params); // ditch this, entirely -- this should be the job of the php side of the client

        // // skim the allowable parameters out of the provided parameters
        // // to prevent passing parameters we do not want to accept here
        // $paramsCleaned = \ClubSpeed\Utility\Params::cleanParams(
        //       $columns['required
        //     , $columns['allowed
        //     , $params
        // );

        $customer = $this->interface->dummy($params);
        

        // note that $params have already been mapped by racers.php

        // run special validation checks / formatting on any existing paPrameters
        // note that if the parameter is set and not empty after being cleaned,
        // we can assume that it was not required based on kiosk settings, and thus
        // does not need to be checked for specific validation.


        if (!isset($customer->FName) || empty($customer->FName))
            throw new \RequiredArgumentMissingException("Customer create received a null or empty FName!");
        if (!isset($customer->LName) || empty($customer->LName))
            throw new \RequiredArgumentMissingException("Customer create received a null or empty LName!");
        if (!isset($customer->BirthDate) || empty($customer->BirthDate))
            throw new \RequiredArgumentMissingException("Customer create received a null or empty BirthDate!");
        if (!isset($customer->Gender)) // Gender of 0 is allowed, and PHP considers this to be empty - only check for isset
            throw new \RequiredArgumentMissingException("Customer create received a null Gender!");

        // validate email
        if (isset($customer->EmailAddress) && !empty($customer->EmailAddress)) {

            // check the email formatting
            if (!\ClubSpeed\Security\Authenticate::isValidEmailFormat($customer->EmailAddress))
                throw new \InvalidEmailException("Customer create found an invalid EmailAddress! Received: " . $customer->EmailAddress);
            
            // check AllowDuplicateEmail settings
            $settings = $this->getSettings(); // collect kiosk settings
            $allowDuplicateEmail = \ClubSpeed\Utility\Convert::toBoolean($settings['MainEngine']['AllowDuplicateEmail']);
            if (!$allowDuplicateEmail && $this->email_exists($customer->EmailAddress))
                throw new \EmailAlreadyExistsException("Customer create found an email which already exists! Received: " . $customer->EmailAddress);
        }

        // validate password strength, then hash it
        if (isset($customer->Password) && !empty($customer->Password)) {
            if (!\ClubSpeed\Security\Authenticate::isAllowablePassword($customer->Password))
                throw new \InvalidArgumentException("Customer create found a password which is not strong enough!");
            $customer->Password = \ClubSpeed\Security\Hasher::hash($customer->Password);
        }

        // convert the gender to the expected gender "id" on the database
        if (isset($customer->Gender) && !empty($customer->Gender)) {
            $gender = strtolower($customer->Gender);
            $genderChar = $gender[0];
            switch ($genderChar) {
                case "m": // male
                    $customer->Gender = 1;
                    break;
                case "f": // female
                    $customer->Gender = 2;
                    break;
                case "o": // other
                    $customer->Gender = 0;
                    break;
            }
        }

        // if RacerName is missing, use FName + " " + LName
        if (!isset($customer->RacerName) || empty($customer->RacerName)) {
            $customer->RacerName = $customer->FName . " " . $customer->LName;
        }

        // grab the CustID using an internal SQL call (can't be done using @@IDENTITY or anything of the like, due to the IDs matching with a LocationID)
        $customer->CustID = $this->getNextCustId(); //$CustID;
        
        // insert the customer record
        $this->interface->create($customer);
        
        // check to see if replication is necessary
        if ($this->logic->replication->isReplicationEnabled()) { // put the isenabled check inside the insert function?
            $this->logic->replication->insertReplicationLogs($customer->CustID, 'Customers', 'insert');
        }

        return $customer->CustID; // need to return this way, since dbo.Customers does not use an autoincrement ID
    }

    /**
     * Adds a customer id to the standard customer queue.
     *
     * @param int $customerId The id of the customer to delete.
     * @return int The quantity of deleted customers.
     * @throws InvalidArgumentException If the customer id provided is not an integer.
     * @throws CustomerNotFoundException If the customer id could not be found in the database.
     */
    public final function add_to_queue($customerId) {
        if (!isset($customerId) || !is_int($customerId))
            throw new \InvalidArgumentException("Customer add to queue requires numeric customerId! Received: $customerId");
        if (!$this->customer_exists($customerId))
            throw new \CustomerNotFoundException("Unable to find customer in the database! Received customerId: $customerId ");

        $sql = "DECLARE @CustID INT; SET @CustID  = ?"
            ."\nIF NOT EXISTS("
            ."\n    SELECT q.CustID"
            ."\n    FROM dbo.Queues q"
            ."\n    WHERE q.CustID = @CustID"
            ."\n)"
            ."\nBEGIN"
            ."\n    INSERT INTO dbo.Queues("
            ."\n        CustID"
            ."\n        , DateAdded"
            ."\n    )"
            ."\n    VALUES ("
            ."\n        @CustID"
            ."\n        , GETDATE()" // DateAdded
            ."\n    );"
            ."\nEND"
            ;
        $params = array(
            $customerId
        );
        $this->db->exec($sql, $params);
    }

    // public final function all() {
    //     return $this->db->customers->all();
    // }

    // public final function get($customerId) {
    //     return $this->db->customers->get($customerId);
    // }

    // public final function update($params = array()) {
    //     return $this->db->customers->update($params);
    // }
}