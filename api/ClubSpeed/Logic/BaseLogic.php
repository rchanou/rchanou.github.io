<?php

namespace ClubSpeed\Logic;

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
        return array(
            $this->interface->key => $id
        );
    }

    public function all($params = array()) {
        return $this->interface->all();
    }

    public function get($id) {
        $get = $this->interface->get($id);
        if (is_null($get) || empty($get))
            throw new \RecordNotFoundException("Looking for record -- " . $this->interface->table . ".[" . $this->interface->key . "] = " . $id);
        return $get;
    }

    public function match($params) {
        return $this->interface->match($params);
    }

    public function find($comparators) {
        return $this->interface->find($comparators);
    }

    public function update($id, $params = array()) {
        return $this->_update($id, $params, null);
    }

    protected function _update($id, $params = array(), $callback = null) {
        $get = $this->get($id);
        $old = $get[0];
        $new = $this->interface->dummy((array)$old);
        $updatable = $this->updatable($params); // throw exception if updatable is empty?
        $new->load($updatable);
        if (!is_null($callback) && is_callable($callback))
            $new = $callback($old, $new);
        $this->interface->update($new);
    }

    public function delete($id) {
        if (!$this->exists($id))
            throw new \RecordNotFoundException("Looking for record -- " . $this->interface->table . ".[" . $this->interface->key . "] = " . $id);
        return $this->interface->delete($id);
    }

    public function exists($id) {
        return $this->interface->exists($id);
    }

    protected function insertable($mapped) {
        // assume that if insertable is not set,
        // then all properties are available for create
        if (empty($this->insertable))
            return $mapped; 
        return $this->restrict($this->insertable, $mapped);
    }

    protected function updatable($mapped) {
        // assume that if insertable is not set,
        // then all properties are available for update
        if (empty($this->updatable))
            return $mapped;
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