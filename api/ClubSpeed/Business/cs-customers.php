<?php

namespace ClubSpeed\Business;
require_once(__DIR__.'../../Utility/Convert.php');
require_once(__DIR__.'../../../Settings.php');
require_once(__DIR__.'../../../Queues.php');

/**
 * The business logic class
 * for ClubSpeed customers.
 */
class CSCustomers {

    /**
     * A reference to the Restler Settings class
     * instantiated during the CSCustomers constructor
     * in order to give this class access to
     * already existing settings collection methods.
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
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSCustomers class.
     *
     * The CSCustomers constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
        $this->settings = new \Settings();
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

        $columns = $this->checkControlPanelForRequiredAndAllowedColumns($params);

        // skim the allowable parameters out of the provided parameters
        // to prevent passing parameters we do not want to accept here
        $paramsCleaned = \ClubSpeed\Utility\Params::cleanParams(
              $columns['required']
            , $columns['allowed']
            , $params
        );

        // run special validation checks / formatting on any existing parameters
        // note that if the parameter is set and not empty after being cleaned,
        // we can assume that it was not required based on kiosk settings, and thus
        // does not need to be checked for specific validation.

        // validate email
        if (isset($paramsCleaned['EmailAddress']) && !empty($paramsCleaned['EmailAddress'])) {

            // check the email formatting
            if (!\ClubSpeed\Security\Validate::isValidEmailFormat($paramsCleaned['EmailAddress']))
                throw new \InvalidEmailException("Customer create found an invalid EmailAddress! Received: " . $paramsCleaned['EmailAddress']);
            
            // check AllowDuplicateEmail settings
            $settings = $this->getSettings(); // collect kiosk settings
            $allowDuplicateEmail = \ClubSpeed\Utility\Convert::toBoolean($settings['MainEngine']['AllowDuplicateEmail']);
            if (!$allowDuplicateEmail && $this->email_exists($paramsCleaned['EmailAddress']))
                throw new \EmailAlreadyExistsException("Customer create found an email which already exists! Received: " . $paramsCleaned['EmailAddress']);
        }

        // validate password strength, then hash it
        if (isset($paramsCleaned['Password']) && !empty($paramsCleaned['Password'])) {
            if (!\ClubSpeed\Security\Validate::isAllowablePassword($paramsCleaned['Password']))
                throw new \InvalidArgumentException("Customer create found a password which is not strong enough!");
            $paramsCleaned['Password'] = \ClubSpeed\Security\Hasher::hash($paramsCleaned['Password']);
        }
        
        // if BirthDate is provided, then run it through the conversion utility class
        if (isset($paramsCleaned['BirthDate']) && !empty($paramsCleaned['BirthDate']))
            $paramsCleaned['BirthDate'] = \ClubSpeed\Utility\Convert::toDateForServer($paramsCleaned['BirthDate']);

        // convert the gender to the expected gender "id" on the database
        if (isset($paramsCleaned['Gender']) && !empty($paramsCleaned['Gender'])) {
            $gender = strtolower($paramsCleaned['Gender']);
            $genderChar = $gender[0];
            switch ($genderChar) {
                case "m": // male
                    $paramsCleaned['Gender'] = 1;
                    break;
                case "f": // female
                    $paramsCleaned['Gender'] = 2;
                    break;
                case "o": // other
                    $paramsCleaned['Gender'] = 0;
                    break;
            }
        }

        // if RacerName is missing, use FName + " " + LName
        if (!isset($paramsCleaned['RacerName']) || empty($paramsCleaned['RacerName'])) {
            $paramsCleaned['RacerName'] = $paramsCleaned['FName'] . " " . $paramsCleaned['LName'];
        }

        // grab the CustID using an internal SQL call (can't be done using @@IDENTITY or anything of the like, due to the IDs matching with a LocationID)
        $paramsCleaned['CustID'] = $this->getNextCustId(); //$CustID;

        //------------------
        // Database methods
        //------------------

        // loop through the cleaned parameters and build them into an insert statement
        $paramsNames  = array();
        $paramsPlaces = array();
        $paramsValues = array();
        foreach($paramsCleaned as $pName => $pValue) {
            if (isset($pValue)) {
                $paramsNames[]  = $pName;
                $paramsPlaces[] = "?";
                $paramsValues[] = $pValue;
            }
        }
        $sql = array(
              "INSERT INTO CUSTOMERS ("
            , "    " . implode(", ", $paramsNames)
            , ")"
            , "VALUES ("
            , "    " . implode(", ", $paramsPlaces)
            , ")"
        );
        $sql = implode("\n", $sql);

        // execute the insert statement
        $this->db->exec($sql, $paramsValues);

        // check to see if replication is necessary
        if ($this->logic->replication->isReplicationEnabled()) { // put the isenabled check inside the insert function?
            $this->logic->replication->insertReplicationLogs($paramsCleaned['CustID'], 'Customers', 'insert');
        }
        return $paramsCleaned['CustID'];
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

    public final function all() {
        return $this->db->customers->all();
    }

    public final function get($customerId) {
        return $this->db->customers->get($customerId);
    }

    public final function update($params = array()) {
        return $this->db->customers->update($params);
    }
}