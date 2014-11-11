<?php

namespace ClubSpeed\Database;

class DbCollection {

    protected $conn;
    public $definition;

    public $key;
    public $table;
    
    public function __construct(&$conn, &$definition) {
        $this->conn = $conn;
        $this->definition = new \ReflectionClass($definition);
        $this->key = $this->definition->getStaticPropertyValue('key');
        $this->table = $this->definition->getShortName();
    }

    public function blank() {
        return $this->definition->newInstance();
    }

    public function dummy($params = array()) {
        return $this->definition->newInstance($params);
    }

    public function create($data) {
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data;
        else
            $record = $this->definition->newInstance($data);
        $record->validate('insert');
        $insert = \ClubSpeed\Database\Helpers\SqlBuilder::buildInsert($record);
        $lastId = $this->conn->exec($insert['statement'], $insert['values']);
        $lastId = \ClubSpeed\Utility\Convert::toNumber($lastId);
        return $lastId;
    }

    public function batchCreate($data) {
        if (is_array($data) && !empty($data)) {
            $records = array();
            foreach($data as $key => $record) {
                $records[$key] = (is_object($record) && $this->definition->isInstance($record) ? $record : $this->definition->newInstance($record));
            }
        }
        $ids = array();
        foreach($records as $key => $record) {
            // note -- if we need more performance out of this,
            // we can build this into a single insert statement
            // by using something similar to the following line:
            //      $batch = \ClubSpeed\Database\Helpers\SqlBuilder::buildBatchInsert($records);
            // 
            // for now, just looping and running creates on each
            try {
                $ids[$key] = $this->create($record);
            }
            catch (\Exception $e) {
                $ids[$key] = array("error" => $e->getMessage());
            }
        }
        return $ids;
    }

    public function all() {
        $select = \ClubSpeed\Database\Helpers\SqlBuilder::buildSelect($this->definition->newInstance());
        $results = $this->conn->query($select['statement']);
        $all = array();
        foreach($results as $result) {
            $all[] = $this->definition->newInstance($result);
        }
        return $all;
    }

    public function get() {
        $ids = func_get_args();
        $instance = $this->definition->newInstanceArgs($ids);
        $get = \ClubSpeed\Database\Helpers\SqlBuilder::buildGet($instance);
        $results = $this->conn->query($get['statement'], $get['values']);
        $get = array();
        foreach($results as $result) {
            $get[] = $this->definition->newInstance($result);
        }
        if (empty($get))
            return null;
        return $get;
        // if (isset($results) && count($results) > 0)
        //     return $this->definition->newInstance($results[0]);
        // return null;
    }

    public function match($data) {
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data;
        else
            $record = $this->definition->newInstance($data);
        $match = \ClubSpeed\Database\Helpers\SqlBuilder::buildFind($record); // note we are using buildFind at this point
        $results = $this->conn->query($match['statement'], @$match['values']);
        $match = array();
        foreach($results as $result) {
            $match[] = $this->definition->newInstance($result);
        }
        return $match;
    }

    public function find($comparators = array()) {
        if (!$comparators instanceof \ClubSpeed\Database\Helpers\GroupedComparator)
            $comparators = new \ClubSpeed\Database\Helpers\GroupedComparator($comparators);
        if (!$this->validateComparators($comparators))
            throw new \CSException("Unable to validate querystring comparators! Check the syntax of the filter querystring.");
        $find = \ClubSpeed\Database\Helpers\SqlBuilder::buildFind($this->definition->newInstance(), $comparators);
        $results = $this->conn->query($find['statement'], @$find['values']);
        $find = array();
        foreach($results as $result) {
            $find[] = $this->definition->newInstance($result);
        }
        return $find;
    }

    public function query($sql, $params = array()) {
        // run an inline query directly without building statements
        // assume that the return of this query will be
        // convertable to the DbCollection type
        $results = $this->conn->query($sql, $params);
        $query = array();
        foreach($results as $result) {
            $query[] = $this->definition->newInstance($result);
        }
        return $query;
    }

    public function update($data) {
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data;
        else
            $record = $this->definition->newInstance($data);
        $update = \ClubSpeed\Database\Helpers\SqlBuilder::buildUpdate($record);
        $affected = $this->conn->exec($update['statement'], @$update['values']);
        // return $affected;
    }

    // note that $args could contain any one of the following:
    //  1. object of the proper instance
    //  2. array representation of the underlying object
    //  3. variable number of primary keys
    public function delete($data) {
        $args = func_get_args();
        $data = reset($args);
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data;
        // else if (is_array($data))
            // $record = $this->definition->newInstance($data);
        else
            $record = $this->definition->newInstanceArgs($args);
        $delete = \ClubSpeed\Database\Helpers\SqlBuilder::buildDelete($record);
        $affected = $this->conn->exec($delete['statement'], $delete['values']);
        // return $affected;
    }

    public function exists() {
        $args = func_get_args();
        $data = reset($args);
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data; // if the first arg is already the right instance, just use it
        else
            $record = $this->definition->newInstanceArgs($args); // otherwise, pass the stuff on -- note that this may be multiple ids (hence, the arg issues)
        $exists = \ClubSpeed\Database\Helpers\SqlBuilder::buildExists($record);
        $results = $this->conn->query($exists['statement'], $exists['values']);
        if (!is_null($results) && !empty($results) && isset($results[0])) {
            $result = $results[0];
            if (isset($result['Exists'])) {
                return \ClubSpeed\Utility\Convert::toBoolean($result['Exists']);
            }
        }
        return false;
    }

    public function validateComparators($comparators) {
        if (!$comparators->validate())// validate structure
            throw new \CSException('Unable to validate comparator structure!');
            // return false;
        foreach($comparators->comparators as $key => $val) { // validate column names
            // at least one of the filter items must be a column name
            // allow both to be column names?
            if (!$this->definition->hasProperty($val['comparator']->left) && !$this->definition->hasProperty($val['comparator']->right))
                return false;
        }
        return true;
    }
}