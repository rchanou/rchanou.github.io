<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Database\Records\BaseRecord;

/**
 * The base class for ClubSpeed API logic classes.
 */
abstract class BaseLogic {

    /**
     * A reference to the parent ClubSpeed logic service.
     */
    protected $logic;

    /**
     * A reference to the injected ClubSpeed database service.
     */
    protected $db;

    /**
     * A whitelist of updatable columns.
     * If none are provided, then all columns (other than the primary key)
     * are assumed to be allowed to be updated through the API.
     */
    protected $updatable;

    /**
     * A whitelist of insertable columns.
     * If none are provided, then all columns (other than the primary key)
     * are assumed to be allowed to be inserted through the API.
     */
    protected $insertable;

    public function __construct(&$logic, &$db) {
        $this->logic = $logic;
        $this->db = $db;
        $this->updatable = array(); // okay, since parent constructor gets called first
        $this->insertable = array(); // okay, same as above
    }

    public function dummy($params = array()) {
        return $this->interface->dummy($params); // do we want to expose this all the way up to logic? probably
    }

    public function create($params = array()) {
        return $this->_create($params, null);
    }

    protected function _create($params = array(), $callback = null) {
        $insertable = $this->insertable($params);
        if (empty($insertable))
            throw new \InvalidArgumentValueException("Create " . $this->interface->table . " did not receive any allowed insertable values!");
        $dummy = $this->interface->dummy($insertable);
        if (!is_null($callback) && is_callable($callback))
            $dummy = $callback($dummy);
        $id = $this->interface->create($dummy);
        $key = $this->interface->key;
        if (!is_array($key)) {
            return array(
                $key => $id
            );
        }
        return array(); // what to do with composite primary keys? just leaving blank for now.
    }

    public function all($params = array()) {
        return $this->interface->all();
    }

    public function get(/* id1, id2, ...*/) {
        $args = func_get_args();
        $get = call_user_func_array(array($this->interface, 'get'), $args);
        if (is_null($get) || empty($get))
            throw new \RecordNotFoundException("Unable to find record on " . $this->interface->table . " with key: (" . implode(",", $args) . ")");
        return $get;
    }

    public function match($params) {
        return $this->interface->match($params);
    }

    public function find($comparators) {
        return $this->interface->find($comparators);
    }

    public function update() {
        $callback = null;
        $params = array();
        $args = func_get_args();
        $getArgs = array();
        $data = array_pop($args);
        if (is_callable($data)) {
            $callback = $data;
            $data = array_pop($args);
        }
        if ($data instanceof BaseRecord || is_array($data))
            $params = $data;
        else {
            // always problematic? no params received
            // if we use the code below, then the keys will be flipped
            $getArgs[] = $data; // is this always a problematic path? no params received
        }
        while($data = array_pop($args))
            array_unshift($getArgs, $data);
        $updatable = $this->updatable($params); // throw exception if updatable is empty?
        if (empty($updatable))
            throw new \InvalidArgumentValueException("Update on " . $this->interface->table . " did not receive any allowed updatable values!");
        $get = call_user_func_array(array($this, 'get'), $getArgs);
        $old = $get[0];
        $new = $this->interface->dummy((array)$old);
        $new->load($updatable);
        if (!is_null($callback) && is_callable($callback))
            $new = $callback($old, $new);
        $this->interface->update($new);
    }

    public function delete() {
        $args = func_get_args();
        if (!call_user_func_array(array($this, 'exists'), $args))
            throw new \RecordNotFoundException("Unable to find record on " . $this->interface->table . " with key: (" . implode(",", $args) . ")");
        call_user_func_array(array($this->interface, 'delete'), $args); // just call, don't return?
    }

    public function exists() {
        $args = func_get_args();
        return call_user_func_array(array($this->interface, "exists"), $args);
    }

    protected function insertable($mapped) {
        // assume that if insertable is not set,
        // then all properties are available for create
        if (empty($this->insertable))
            return (array)$mapped; 
        return $this->restrict($this->insertable, $mapped);
    }

    protected function updatable($mapped) {
        // assume that if insertable is not set,
        // then all properties are available for update
        if (empty($this->updatable))
            return (array)$mapped;
        return $this->restrict($this->updatable, $mapped);
    }

    private function restrict($usable, $mapped) {
        if (!is_array($mapped))
            $mapped = (array)$mapped; // convert dummy objects to arrays for foreach syntax to always work
        $return = array();
        foreach($usable as $val) {
            if (isset($mapped[$val]) && $val != $this->interface->key)
                $return[$val] = $mapped[$val];
        }
        return $return;
    }
}