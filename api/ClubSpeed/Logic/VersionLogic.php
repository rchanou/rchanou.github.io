<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert as Convert;
use ClubSpeed\Connection as Connection;

/**
 * The business logic class
 * for ClubSpeed versions.
 */
class VersionLogic extends BaseLogic {

    private $toNumber = "ClubSpeed\Utility\Convert::toNumber"; // php nonsense - array_map works with a string or a closure, but not a pointer to the function

    /**
     * Constructs a new instance of the VersionLogic class.
     *
     * The VersionLogic constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the LogicContainer from which this class will been loaded.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->version_CS;
    }

    private function _expand($val) {
        if (is_string($val))
            return array_map($this->toNumber, explode('.', $val)); // explode string, convert pieces to numbers
        else if (is_array($val))
            return array_map($this->toNumber, $val); // don't assume val is already an array of numbers - convert to be safe
        else if (is_numeric($val))
            return array($val); // if a single number is passed, assume it is the major version
        return $val; // or throw error? unexpected type !!!
    }

    public function current($expand = true) {
        $versions = parent::all();
        $current = end($versions); // note that this isn't really necessary -- all rows in the Version_CS table contain the correct CurrentVersion
        $current = $current->CurrentVersion;
        if ($expand)
            $current = $this->_expand($current); // turn into an array for comparison purposes
        // else, return as a string for whatever reason (logging, etc)
        return $current;
    }

    public function compareToCurrent($version) {
        return $this->compare($this->current(), $version);
    }

    public function compare($a, $b) {
        $a = $this->_expand($a);
        $b = $this->_expand($b);
        // if minor version numbers are missing, fill with 0s
        while(count($a) < count($b))
            $a[] = 0;
        while(count($b) < count($a))
            $b[] = 0;
        $length = count($a); // lengths should now be the same
        for($i = 0; $i < $length; $i++) {
            if ($a[$i] < $b[$i])
                return -1; // version a is less than version b
            else if ($a[$i] > $b[$i])
                return 1; // version a is greater than version b
        }
        return 0; // version a is equivalent to version b
    }

    public function hasEurekas() {
        // note this lovely bit of hackery
        // this is due to the fact that SQL server pdo does not respect PDO::ATTR_TIMEOUT properly
        // and will hang for 15-20 seconds if we try connecting to the database directly
        try {
            $conn = new Connection\ClubSpeedConnection(); 
            $conn->exec('USE RestaurantPiece;');
            return true;
            // this will throw exception if RestaurantPiece is not available.
        }
        catch (\Exception $e) {
            return false;
        }
    }
}