<?php

namespace ClubSpeed\Security;
require_once(__DIR__.'./password-compat.php');

/**
 * A static class used to contain the methods
 * for hashing, validating, and re-hashing passwords.
 */
class Hasher {

    /**
     * @const COST The cost coefficient to pass to the Bcrypt algorithm as a default.
     */
    const COST = 10.0;

    /**
     * @const ALGORITHM The constant representation of the algorithm to use inside the password_hash function.
     */
    const ALGORITHM = PASSWORD_BCRYPT;

    /**
     * Dummy constructor to prevent any initialization of the Hasher Class
     */
    private function __construct() {}
    
    /**
     * Returns a hash for the provided password.
     *
     * @param string $password The original password.
     * @return string The hashed password.
     */
    public static function hash($password, $cost = self::COST, $salt = null) {
        $options = array();
        $options['cost'] = $cost;
        if (isset($salt)) {
            $options['salt'] = $salt;
        }
        return password_hash(
            $password
            , self::ALGORITHM
            , $options
        );
    }

    /**
     * Verifies a provided password and hash combination.
     *
     * @param string $password The original password.
     * @param string $hash The stored hash.
     * @return bool If the newly hashed password matches the provided hash, then true. Else, false.
     */
    public static function verify($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Rehashes a password. 
     * This method could be used when converting to a new hashing method,
     * or could be re-used each time a user logs in to cycle through salts/hashes.
     *
     * Note that this method is not being utilized at this time,
     * but will need to be considered in the future if moving to a new algorithm.
     *
     * @param string $password The original password.
     * @param string $hash The stored hash.
     * @return bool True if the newly hashed password matches the provided hash, else false.
     */
    public static function rehash($password, $hash) {
        if (password_verify($password, $hash)) {
            // password_needs_rehash($password, $hash) should be used in order to detect an algorithm update
            // do we need a callback here to handle storing the hash in the database?
            // or should we use multiple returns (array with multiple keys) for a verification check, as well as the new hash?
            // $callback(self::hash($password)); return true;
            return self::hash($password); 
        }
        return false;
    }
}