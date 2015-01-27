<?php

namespace ClubSpeed\Security;
use ClubSpeed\Enums\Enums as Enums;

/**
 * The ClubSpeed validation class, used for determining whether or not
 * API calls are being made with the properly required access.
 */
class Authenticate {

    /**
     * Boolean flag to determine whether or not the initialize method has been called
     * to prevent any attempted validation methods without first injecting the DbConnection.
     */
    private static $isInitialized = false;

    /**
     * A reference to the DbConnection class to be injected on initialization.
     */
    private static $logic = null;

    /**
     * The minimum length for a password.
     */
    const PASSWORD_MINIMUM_LENGTH = 8;

    /**
     * The maximum length for a password.
     *
     * Note that this is being set since
     * the bcrypt algorithm truncates 
     * password inputs to 72 characters.
     */
    const PASSWORD_MAXIMUM_LENGTH = 72; // bcrypt will truncate past 72 characters. allow users to go this high?

    /**
     * Dummy constructor to prevent any initialization of the Validate Class
     */
    private function __construct() {} // prevent any initialization of this class

    /**
     * Initialize the validation class by injecting an instance
     * of CSLogic for validation purposes.
     *
     * Note that the CSLogic is expected to contain a Users class
     * which will validate a username and password against the database
     * by utilizing a method named validate.
     * 
     * @param CSLogic &$CSLogic The reference to the CSLogic class.
     * @return void
     */
    public static function initialize(&$logic) {
        if (!self::$isInitialized) {
            if (!($logic instanceof \ClubSpeed\Logic\LogicService)) {
                $received = gettype($logic);
                if ($received === "object")
                    $received = get_class($logic);
                throw new \InvalidArgumentException("Attempted to initialize Validate class by injecting an object other than a LogicContainer! Received: " . $received);
            }
            self::$logic = $logic;
            self::$isInitialized = true;
        }
    }

    /**
     * Verifies the credentials of the current session by comparing public keys,
     * and (if necessary) by validating the username and password through DbConnection->users.
     * 
     * @return boolean True if the credentials are valid for at least public access, false if not.
     */
    public static function publicAccess() {
        $credentials = self::getCredentials();
        if (self::isValidPublicKey($credentials['key']))
            return true;
        if (self::isValidUser($credentials['username'], $credentials['password']))
            return true;
        return false;
    }

    public static function customerAccess($customerId) {
        $credentials = self::getCredentials();
        if (self::isValidPrivateKey($credentials['key']))
            return true;
        if (self::isValidCustomerKey($credentials['key'], $customerId))
            return true;
        if (self::isValidUser($credentials['username'], $credentials['password']))
            return true;
        return false;
    }

    /**
     * Verifies the credentials of the current session by comparing public keys,
     * and (if necessary) by validating the username and password through DbConnection->users.
     * 
     * @return boolean True if the credentials are valid for at least public access, false if not.
     */
    public static function privateAccess() {
        $credentials = self::getCredentials();
        if (self::isValidPrivateKey($credentials['key']))
            return true;
        if (self::isValidUser($credentials['username'], $credentials['password']))
            return true;
        return false;
    }

    /**
     * Collects credentials for the current session.
     *
     * @return string[string] An associative array containing the current username, password, and key.
     */
    private static function getCredentials() {
        return array(
              'username'    => @$_SERVER['PHP_AUTH_USER'] // basic auth decoded user
            , 'password'    => @$_SERVER['PHP_AUTH_PW'] // basic auth decoded password
            , 'key'         => @$_REQUEST['key'] // query string key
        );
    }

    /**
     * Checks a provided username and password for validation
     * by using the users->validate method of the injected $logic class.
     *
     * @param string $username The username to use for validation.
     * @param string $password The password to use for validation.
     * @return boolean True if a valid set of credentials, false if not.
     */
    private static function isValidUser(&$username, &$password) {
        if (!self::$isInitialized)
            throw new \LogicException("Error: attempted a call to \ClubSpeed\Security\Validate\isValidUser before initializing!");
        if (isset($username) && isset($password))
            return self::$logic->users->validate($username, $password);
        return false;
    }

    /**
     * Checks for a valid public key by checking existence
     * and a comparison between the list of all available public api keys
     * built by config.php and stored in $GLOBALS['authentication_keys'].
     *
     * @param string $key The key to validate for public access.
     * @return boolean True if the key has at least public access, false if not.
     */
    private static function isValidPublicKey(&$key) {
        if (isset($key) && in_array($key, $GLOBALS['authentication_keys']) && $key != md5(date('Y-m-d')))
            return true;
        // fall through is awkward and hacky, since we don't have roles (or multi-roles),
        // or the ability to compare access of those roles with proper chains.
        // just consider any bearer of a token to have at least public access.
        $authenticationToken = self::$logic->authenticationTokens->match(array(
            'Token' => $key
        ));
        if (!empty($authenticationToken))
            return true;
        return false;
    }

    /**
     * Checks for a valid private key by checking existence
     * and a comparison between the globally stored private key
     * defined by config.php and stored in $GLOBALS['privateKey'].
     *
     * @param string $key The key to validate.
     * @return boolean True if the key has at least private access, false if not.
     */
    private static function isValidPrivateKey(&$key) {
        if (isset($key) && $key === $GLOBALS['privateKey'])
            return true;
        return false;
    }

    private static function isValidCustomerKey(&$key, $customerId) {
        // not super extendable, but sufficient for now.
        if (empty($key))
            return false;
        if (empty($customerId))
            return false;
        $authenticationToken = self::$logic->authenticationTokens->match(array(
              'CustomersID' => $customerId
            , 'Token'       => $key
            , 'TokenType'   => Enums::TOKEN_TYPE_CUSTOMER
        ));
        if (!empty($authenticationToken))
            return true;
        return false;
    }

    /**
     * Validates the structure of the provided email.
     *
     * @param string $email The email to be validated.
     * @return mixed Truthy if valid, else falsy.
     */
    public static function isValidEmailFormat($email) {
        // note that the built in php email validation filter
        // will fail on certain emails which are permissible according to RFC5321.
        // if this is not sufficient, we can look into is_email() - http://isemail.info/about
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validates the structure of the provided password.
     *
     * @param string $password The password to be validated.
     * @return boolean True if allowable, else false.
     */
    public static function isAllowablePassword($password) {
        $lowerCase = 0;
        $upperCase = 0;
        $number = 0;
        $symbol = 0;

        // todo: enforce password requirements such as length, character types, etc
        return true;
    }
}