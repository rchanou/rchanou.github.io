<?php

namespace ClubSpeed\Logic;
require_once(__DIR__.'/../../Settings.php');
require_once(__DIR__.'/../../Queues.php');

use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Utility\Convert as Convert;
use ClubSpeed\Utility\Tokens as Tokens;
use ClubSpeed\Security\Hasher as Hasher;

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
        $customerExists = Convert::toBoolean($results[0]['CustomerExists']);
        return $customerExists;
    }

    /**
     * Attempts to log in a customer using the provided authentication token.
     *
     * @param string $token The authentication token for the login.
     */
    public final function authenticate($token) {
        if (!isset($token) || !is_string($token))
            throw new \InvalidArgumentException("Customer authenticate requres token to be a non-empty string!");
        $authenticationTokens = $this->logic->authenticationTokens->match(array(
              'TokenType' => Enums::TOKEN_TYPE_CUSTOMER
            , 'Token'     => $token
        ));
        if (empty($authenticationTokens))
            throw new \UnauthorizedException();
        $authenticationToken = $authenticationTokens[0];

        // automatically update the ExpiresAt on the token, or allow it to expire?
        $this->logic->authenticationTokens->update($authenticationToken->AuthenticationTokensID, $authenticationToken); // logic class will update ExpiresAt automatically
        // potential security issue?
        // if an unauthorized member gets hold of an authentication token,
        // they can use this method over and over to ensure it stays valid forever,
        // unless we implement the standard refresh tokens (potentially - not convinced about their security)
        // or we allow the token to continue on to expiration by removing the update above.
        return array(
              'customerId' => $authenticationToken->CustomersID
            , 'token'      => $authenticationToken->Token
        );
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
     * @throws UnauthorizedException    If the email provided could not be found in the database.
     * @throws UnauthorizedException    If the provided password does not match the hash stored in the database.
     */
    public final function login($email, $password) {
        if (!isset($email) || !is_string($email))
            throw new \InvalidArgumentException("Customer login requires email to be a string! Received: $email");
        if (!isset($password) || !is_string($password))
            throw new \InvalidArgumentException("Customer login requires password to be a string!");

        $primaryCustomer = $this->find_primary_account($email); // ~ 143ms
        if(empty($primaryCustomer)) {
            // Customer email could not be found in the database
            throw new \UnauthorizedException("Invalid credentials!");
        }
        $customerId = $primaryCustomer->CustID; // password is not exposed on primaryCustomer -- grab the underlying customer record
        $customer = $this->interface->get($customerId);
        $customer = $customer[0];
        if (!Hasher::verify($password, $customer->Hash))
            throw new \UnauthorizedException("Invalid credentials!");
        $authentication = $this->db->authenticationTokens->match(array(
              'CustomersID' => $primaryCustomer->CustID
            , 'TokenType'   => Enums::TOKEN_TYPE_CUSTOMER
        ));
        if (empty($authentication)) {
            // no record found - make a new one
            $token = Tokens::generate();
            $this->logic->authenticationTokens->create(array(
                'CustomersID'       => $primaryCustomer->CustID
                , 'TokenType'       => Enums::TOKEN_TYPE_CUSTOMER
                , 'RemoteUserID'    => 1 // what to do with this?
                , 'Token'           => $token
            ));
        }
        else {
            // record was found - update the expiration date
            $authentication = $authentication[0];
            $token = $authentication->Token; // store the token to return, but don't change it (in case the user is logging in on multiple devices with the same account)
            $this->logic->authenticationTokens->update($authentication->AuthenticationTokensID, $authentication); // logic class will update ExpiresAt automatically
        }
        return array(
              "customerId"  => $primaryCustomer->CustID
            , "token"       => $token
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

        // cheat! point this to PrimaryCustomersLogic, as a temporary fix
        $primaryCustomer = $this->logic->primaryCustomers->match(array('EmailAddress' => $email)); // ~ 147ms
        if (empty($primaryCustomer))
            return array();
        return $primaryCustomer[0];
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
    public final function create_v0($params = array()) {
        // bit of an oddball - note that $params have already been mapped by racers.php
        $customer = $this->interface->dummy($params);

        // run special validation checks / formatting on any existing parameters
        // note that if the parameter is set and not empty after being cleaned,
        // we can assume that it was not required based on kiosk settings, and thus
        // does not need to be checked for specific validation.

        if (!isset($customer->FName) || empty($customer->FName))
            throw new \RequiredArgumentMissingException("Customer create received a null or empty FName!");
        if (!isset($customer->LName) || empty($customer->LName))
            throw new \RequiredArgumentMissingException("Customer create received a null or empty LName!");
        // if (!isset($customer->BirthDate) || empty($customer->BirthDate))
            // throw new \RequiredArgumentMissingException("Customer create received a null or empty BirthDate!");
        if (!isset($customer->Gender)) // Gender of 0 is allowed, and PHP considers this to be empty - only check for isset
            throw new \RequiredArgumentMissingException("Customer create received a null Gender!");

        // validate email
        if (isset($customer->EmailAddress) && !empty($customer->EmailAddress)) {

            // check the email formatting
            if (!\ClubSpeed\Security\Authenticate::isValidEmailFormat($customer->EmailAddress))
                throw new \InvalidEmailException("Customer create found an invalid EmailAddress! Received: " . $customer->EmailAddress);
            
            $allowDuplicateEmail = $this->logic->controlPanel->get('MainEngine', 'AllowDuplicateEmail');
            $allowDuplicateEmail = $allowDuplicateEmail[0];
            $allowDuplicateEmail = Convert::toBoolean($allowDuplicateEmail->SettingValue);
            if (!$allowDuplicateEmail && $this->email_exists($customer->EmailAddress))
                throw new \EmailAlreadyExistsException("Customer create found an email which already exists! Received: " . $customer->EmailAddress);
        }

        // validate password strength, then hash it
        if (isset($customer->Hash) && !empty($customer->Hash)) {
            if (!\ClubSpeed\Security\Authenticate::isAllowablePassword($customer->Hash))
                throw new \InvalidArgumentException("Customer create found a password which is not strong enough!");
            $customer->Hash = Hasher::hash($customer->Hash);
        }
        // convert the gender to the expected gender "id" on the database
        if (isset($params['Gender']) && !empty($params['Gender'])) {
            $gender = strtolower($params['Gender']);
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
        if (!isset($customer->RacerName) || empty($customer->RacerName))
            $customer->RacerName = $customer->FName . " " . $customer->LName;

        // grab the CustID using an internal SQL call (can't be done using @@IDENTITY or anything of the like, due to the IDs matching with a LocationID)
        $customer->CustID = $this->getNextCustId(); //$CustID;
        $customer->CrdID = $this->generateCardId();
        
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

    // todo, if necessary
    public function create($params = array()) {
        $newCustId = $this->getNextCustId(); // get out here for scoping issues
        $settings = $this->getSettings(); // get out here for scoping issues

        $db =& $this->db;
        $createReturn = parent::_create($params, function($customer) use (&$db, &$params, &$settings, &$newCustId) {
            // validate email
            if (isset($customer->EmailAddress) && !empty($customer->EmailAddress)) {

                // check the email formatting
                if (!\ClubSpeed\Security\Authenticate::isValidEmailFormat($customer->EmailAddress))
                    throw new \InvalidEmailException("Customer create found an invalid EmailAddress! Received: " . $customer->EmailAddress);
                
                // check AllowDuplicateEmail settings
                // $settings = $self->getSettings(); // collect kiosk settings
                $allowDuplicateEmail = Convert::toBoolean($settings['MainEngine']['AllowDuplicateEmail']);
                if (!$allowDuplicateEmail) {
                    $customersWithEmail = $db->customers->match(array(
                        'EmailAddress' => $customer->EmailAddress
                    ));
                    if (!empty($customersWithEmail))
                        throw new \EmailAlreadyExistsException("Customer create found an email which already exists! Received: " . $customer->EmailAddress);
                }
            }

            // use $params for gender, since creating the customer record will have already converted gender to some sort of int
            if (isset($params['Gender']) && !empty($params['Gender'])) {
                $gender = strtolower($params['Gender']);
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

            // validate password strength, then hash it
            if (isset($customer->Hash) && !empty($customer->Hash)) {
                if (!\ClubSpeed\Security\Authenticate::isAllowablePassword($customer->Hash))
                    throw new \InvalidArgumentException("Customer create found a password which is not strong enough!");
                $customer->Hash = Hasher::hash($customer->Hash);
            }

            // if RacerName is missing, use FName + " " + LName
            if (!isset($customer->RacerName) || empty($customer->RacerName)) {
                $customer->RacerName = $customer->FName . " " . $customer->LName;
            }
            $customer->TotalVisits = 1;

            $customer->CustID = $newCustId;
            $customer->CrdID = $this->generateCardId();

            // check for duplicate emails, or just let it through?
            return $customer;
        });
        if ($this->logic->replication->isReplicationEnabled()) {
            $this->logic->replication->insertReplicationLogs($newCustId, 'Customers', 'insert');
        }
        return array(
            'CustID' => $newCustId
        );
    }

    public function update(/*$id, $params = array()*/) {
        $args = func_get_args();
        $id = $args[0]; // hack it, since we need to use params inside the update closure
        $params = $args[1] ?: array();
        $db =& $this->db;
        $closure = function($old, $new) use (&$db, &$params) {

            // // validate email
            if (isset($new->EmailAddress) && !empty($new->EmailAddress) && $new->EmailAddress != $old->EmailAddress) {

                // check the email formatting
                if (!\ClubSpeed\Security\Authenticate::isValidEmailFormat($new->EmailAddress))
                    throw new \InvalidEmailException("Customer update found an invalid EmailAddress! Received: " . $new->EmailAddress);
                
                // check AllowDuplicateEmail settings
                // $settings = $self->getSettings(); // collect kiosk settings
                $allowDuplicateEmail = Convert::toBoolean($settings['MainEngine']['AllowDuplicateEmail']);
                if (!$allowDuplicateEmail) {
                    $customersWithEmail = $db->customers->match(array(
                        'EmailAddress' => $new->EmailAddress
                    ));
                    if (!empty($customersWithEmail))
                        throw new \EmailAlreadyExistsException("Customer update found an email which already exists! Received: " . $new->EmailAddress);
                }
            }

            // use $params for gender, since creating the dummy customer record will have already converted gender to some sort of int
            if (isset($params['Gender']) && !empty($params['Gender'])) {
                $gender = strtolower($params['Gender']);
                $genderChar = $gender[0];
                switch ($genderChar) {
                    case "m": // male
                        $new->Gender = 1;
                        break;
                    case "f": // female
                        $new->Gender = 2;
                        break;
                    case "o": // other
                        $new->Gender = 0;
                        break;
                }
            }
            if (isset($new->Hash) && !empty($new->Hash)) {
                if (!\ClubSpeed\Security\Authenticate::isAllowablePassword($new->Hash))
                    throw new \InvalidArgumentException("Customer create found a password which is not strong enough!");
                $new->Hash = Hasher::hash($new->Hash);
            }
            return $new;
        };
        array_push($args, $closure);
        return call_user_func_array(array("parent", "update"), $args);
    }

    //TODO: Discuss with Dave where to move this utility function - temporarily duplicated from GiftCardProductHandler
    private function generateCardId() {
        // move to utility class if necessary
        // note that we can't really move it to GiftCardHistoryLogic,
        // since the CrdID lives on the dbo.Customers record
        $cardId = -1;
        while($cardId < 0) {
            // where does the venue id come from? -- it doesn't. just use a random number.
            $tempCardId = mt_rand(1000000000, 2147483647); // get a random 10 digit number, up to the max signed int value
            $customer = $this->logic->customers->find("CrdID = " . $tempCardId);
            if (empty($customer))
                $cardId = $tempCardId; // card id was not being used yet, we can use this one
        }
        return $cardId;
    }
}