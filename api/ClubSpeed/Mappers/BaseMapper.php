<?php

namespace ClubSpeed\Mappers;

class BaseMapper {

    protected $_map;

    public function __construct() {
        $this->_map = array();
    }

    public function in($data = array()) {
        $clean = $this->map('server', new \ClubSpeed\Containers\ParamsContainer($data));
        if (!is_null($clean->select))
            $this->limit('client', $clean->select);
        return $clean;
    }

    public function out($data) {
        return $this->compress($data);
    }

    public function mutate() {
        $args = func_get_args();
        $callbackArgs = array();
        $data = array_pop($args);
        $temp = null;
        if(!empty($args))
            $temp = array_pop($args);
        if (is_array($temp)) {
            $clean = $this->in($temp); // handles select, if available
            if ($clean->hasFilter()) // filters should never be mixed with params
                array_unshift($callbackArgs, $clean->filter);
            else if ($clean->hasParams())
                array_unshift($callbackArgs, $clean->params);
            // else
            //     array_unshift($callbackArgs, null); // only reserved keywords -- push a null on to satisfy closures (??) this throws off closures not expecting any non-reserved data
            if (!empty($args))
                $temp = array_pop($args);
        }
        if (is_int($temp) || is_string($temp)) // will most likely be a string, but needs to be a number? sometimes?
            array_unshift($callbackArgs, $temp);
        while(count($args) > 0) // multiple ids
            array_unshift($callbackArgs, array_pop($args));
        if (is_callable($data))
            $data = call_user_func_array($data, $callbackArgs);
        return $this->out($data);
    }

    public function map($type, $data) {
        if (is_null($type) || empty($type))
            throw new \RequiredArgumentMissingException("MapBase map() received an empty type!");
        if (is_null($data))
            throw new \RequiredArgumentMissingException("MapBase map() received null data!");
        $currentMap = $this->_map[$type];
        if ($data instanceof \ClubSpeed\Containers\ParamsContainer) {
            if (!is_null($data->filter))
                $data->filter = $this->map($type, $data->filter);
            if (!is_null($data->params))
                $data->params = $this->map($type, $this->decompress($data->params));
            // don't map $data->select -- we want this to stay in the client json parameter format
            return $data;
        }
        else if ($data instanceof \ClubSpeed\Database\Helpers\GroupedComparator) {
            foreach($data->comparators as $key => $val) {
                $data->comparators[$key]['comparator'] = $this->map($type, $val['comparator']);
            }
            return $data;
        }
        else if ($data instanceof \ClubSpeed\Database\Helpers\Comparator) {
            if (isset($data->left) && is_string($data->left)) {
                if (isset($currentMap[$data->left]))
                    $data->left = $currentMap[$data->left];
            }
            if (isset($data->right) && is_string($data->right)) {
                if (isset($currentMap[$data->right]))
                    $data->right = $currentMap[$data->right];
            }
            return $data;
        }
        else if (is_array($data) || is_object($data)) {
            $mapped = array();
            foreach($data as $key => $val) {
                if (is_array($val))
                    $mapped[$key] = $this->map($type, $val);
                else {
                    if (isset($currentMap[$key]))
                        $mapped[$currentMap[$key]] = $val;
                }
            }
            return $mapped;
        }
        else {
            // return isset($currentMap[$data]) ? $currentMap[$data] : $data; // safer, but we might want to catch these errors and fix, not let them through
            return $currentMap[$data];
        }
    }

    protected function limit($type, $select = array()) {
        if (isset($select) && !empty($select) && is_array($select)) {
            if (isset($this->_map[$type])) {
                $map =& $this->_map[$type];
                foreach($map as $key => $val) { // could conver this to making a new array and overwriting the old one for performance
                    if (!in_array($val, $select)) {
                        unset($map[$key]);
                    }
                }
            }
        }
    }

    protected function restrict($type, $deselect = array()) {
        if (isset($deselect) && !empty($deselect) && is_array($deselect)) {
            if (isset($this->_map[$type])) {
                $map =& $this->_map[$type];
                foreach($map as $key => $val) { // could conver this to making a new array and overwriting the old one for performance
                    if (in_array($val, $deselect)) {
                        unset($map[$key]);
                    }
                }
            }
        }
    }

    protected function register($serverToClientMap = array(), $clientToServerMap = array()) {
        foreach($serverToClientMap as $key => $val) {
            if (empty($val))
                $serverToClientMap[$key] = str_replace("ID", "Id", lcfirst($key)); // also handle ID -> Id?
        }
        $this->_map['client'] = $serverToClientMap;
        $this->_map['server'] = $clientToServerMap ?: array_flip($serverToClientMap);
    }

    protected static function &array_first(&$arr, $predicate) {
        $return = null;
        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                if (call_user_func($predicate, $arr[$i])) {
                    return $arr[$i];
                }
            }
        }
        return $return;
    }

    protected static function &findExisting(&$data, $key, $val) {
        // this could really be its own helper function
        $existing = null;
        foreach($data as &$current) {
            if (isset($current[$key]) && $current[$key] == $val) {
                $existing =& $current;
                return $existing;
            }
        }
        return $existing;
    }

    protected function decompress($data) {
        return $data;
    }

    protected function is_assoc($array = array()) {
        if (is_array($array)) {
            foreach($array as $key => $val) {
                if (!is_int($key))
                    return true;
            }
        }
        return false;
    }

    protected function compress($data) {
        if (!isset($data) || is_null($data))
            return null;
        $table = $this->namespace ?: "records";
        if ($this->is_assoc($data)) { // for the id => {id} arrays coming from create calls. this seems hacky -- consider another option
            foreach($data as $key => $value) {
                $compressed[$this->map('client', $key)] = $value;
            }
        }
        else {
            $compressed = array(
                $table => array()
            );
            $inner =& $compressed[$table];
            if (isset($data) && !is_array($data))
                $data = array($data);
            if (!empty($data)) {
                foreach($data as $record) {
                    if (!empty($record)) {
                        $inner[] = $this->map('client', $record);
                    }
                } 
            }
        }
        return $compressed;
    }
}