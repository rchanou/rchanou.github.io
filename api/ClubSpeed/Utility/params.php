<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

/**
 * A utility class containing helper methods for dynamic parameters.
 */
class Params {

    private static $reserved = array(
        'key'
        , 'debug'
        , 'select'
        , 'XDEBUG_PROFILE'
    );

    private static $special = array(
        'filter'
    );

    /**
     * Dummy constructor to prevent any initialization of the Validate Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function hasNonReservedData($data) {
        if (is_null($data) || empty($data))
            return false;
        foreach($data as $key => $val) {
            // if (!in_array($key, self::$reserved) && !in_array($key, self::$special) && isset($val)) {
            if (!in_array($key, self::$reserved) && isset($val)) {
                return true;
            }
        }
        return false;
    }

    public static function nonReservedData($data) {
        if (is_null($data) || empty($data))
            return $data;
        foreach(self::$reserved as $val) {
            if (isset($data[$val]))
                unset($data[$val]);
        }
        return $data;
    }

    public static function isFilter($data) {
        if (is_null($data) || empty($data))
            return false;
        if (isset($data['filter']))
            return true;
        return false;
    }

    public static function isWhere($data) {
        if (is_null($data) || empty($data))
            return false;
        if (isset($data['where']))
            return true;
        return false;
    }

    // for use with params coming from the api
    // cleanParams (below) should be deprecated 
    // at some point for the newer structure
    public static function clean($data, $limit = array()) {
        $return = array(
            'params' => array()
        );
        if (is_null($data) || empty($data)) {
            return $return; // leave early?
        }

        foreach($data as $key => $val) {
            // move the limit for insert/update to here? then we have to use json styled-names, instead of database styled-names
            if (
                !in_array($key, self::$reserved)
                && !in_array($key, self::$special)
                && (!empty($limit) ? in_array($key, $limit) : true)
            ) {
                $return['params'][$key] = $val;
            }
        }
        if (isset($data['select'])) {
            foreach(explode(',', $data['select']) as $key => $val) {
                $return['select'][$key] = trim($val);
            }
        }
        if (isset($data['filter']))
            $return['filter'] = $data['filter']; // keep as string for now?

        return $return;
    }

    /**
     * Dynamically skims a list of reqired and allowed parameters out of a list of existing parameters,
     * throwing errors whenever required parameters are missing from the original list.
     *
     * @param string[int]   $requiredParams (optional)  A list of the required parameters.
     * @param string[int]   $allowedParams  (optional)  A list of the allowed parameters.
     * @param mixed[string] $currentParams              The set of parameters to be cleaned.
     * @return mixed[string] The set of cleaned parameters.
     * @throws RequiredArgumentMissingException if there is a required parameter which could not be found in the current parameters.
     */
    public static function cleanParams($requiredParams = array(), $allowedParams = array(), $currentParams = array()) {
        $paramsCleaned = array();
        foreach($requiredParams as $requiredParam) {
            if (!isset($currentParams[$requiredParam]) || $currentParams[$requiredParam] === "") {
                throw new \RequiredArgumentMissingException("Required parameter $requiredParam was missing!");
            }
            $paramsCleaned[$requiredParam] = $currentParams[$requiredParam];
        }
        foreach($allowedParams as $allowedParam) {
            if (isset($currentParams[$allowedParam])) {
                $paramsCleaned[$allowedParam] = $currentParams[$allowedParam];
            }
        }
        return $paramsCleaned;
    }
}