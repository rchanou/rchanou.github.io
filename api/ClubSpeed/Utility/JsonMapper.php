<?php

namespace ClubSpeed\Utility;

class JsonMapper {

    private static $_map = array();

    /**
     * Dummy constructor to prevent any initialization of the JsonMapper Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function register($table, $serverToClientMap, $clientToServerMap = null) {
        if (is_array(self::$_map)) {
            if (!isset(self::$_map[$table])) {
                foreach($serverToClientMap as $key => $val) {
                    if (empty($val))
                        $serverToClientMap[$key] = str_replace("ID", "Id", lcfirst($key)); // also handle ID -> Id?
                }
                self::$_map[$table]['client'] = $serverToClientMap;
                self::$_map[$table]['server'] = $clientToServerMap ?: array_flip($serverToClientMap);
            }
        }
    }

    public static function getMap($table, $type) {
        if (is_null($table) || empty($table))
            throw new \RequiredArgumentMissingException("Json Mapper getMap() received an empty table!");
        if (is_null($type) || empty($type))
            throw new \RequiredArgumentMissingException("Json Mapper getMap() received an empty type!");

        $map = self::$_map;
        if (isset($map[$table])) {
            if (isset($map[$table][$type]))
                return $map[$table][$type];
        }
        return null; // or array()?
    }

    public static function limit($table, $type, $select = array()) {
        if (!empty($select)) {
            if (isset(self::$_map[$table])) {
                if (isset(self::$_map[$table][$type])) {
                    $map =& self::$_map[$table][$type];
                    foreach($map as $key => $val) { // could conver this to making a new array and overwriting the old one for performance
                        if (!in_array($val, $select)) {
                            unset($map[$key]);
                        }
                    }
                }
            }
        }
    }

    public static function map($table, $type, $params = array()) {
        if (is_null($table) || empty($table))
            throw new \RequiredArgumentMissingException("Json Mapper map() received an empty table!");
        if (is_null($type) || empty($type))
            throw new \RequiredArgumentMissingException("Json Mapper map() received an empty type!");

        $mapped = null;
        $currentMap = self::$_map[$table][$type];
        if (is_array($params) || is_object($params)) {
            if ($params instanceof \ClubSpeed\Database\Helpers\GroupedComparator) {
                foreach($params->comparators as $key => $val) {
                    $params->comparators[$key]['comparator'] = self::map($table, $type, $val['comparator']);
                }
                return $params;
            }
            else if ($params instanceof \ClubSpeed\Database\Helpers\Comparator) {
                $mapped = $params; // modify the original object? what about grouped comparators, eventually?
                if (isset($params->left) && is_string($params->left)) {
                    if (isset($currentMap[$params->left]))
                        $mapped->left = $currentMap[$params->left];
                }
                if (isset($params->right) && is_string($params->right)) {
                    if (isset($currentMap[$params->right]))
                        $mapped->right = $currentMap[$params->right];
                }
                return $mapped;
            }
            else {
                $mapped = array();
                foreach($params as $key => $val) {
                    if (isset($currentMap[$key]))
                        $mapped[$currentMap[$key]] = $val;
                }
            }
        }
        else {
            // expected a single name, not multiple params as an associative array -- rename parameter?
            $mapped = $currentMap[$params];
        }
        return $mapped;
    }
}