<?php

namespace ClubSpeed\Database\Collections;

abstract class DbCollection {

    protected $conn;
    protected $definition;

    protected $dbToJson; // expected to be overwritten in the class extension (can't declare properties as abstract in php)
    protected $jsonToDb; // expected to be overwritten in the class extension (can't declare properties as abstract in php)

    public $key;
    
    public function __construct(&$CSConnection) {
        $this->conn = $CSConnection;
    }

    protected function secondaryInit() {
        // to be called by the inherited constructor once internal logic is done
        $this->jsonToDb = array_flip($this->dbToJson);
        $this->key = $this->definition->getStaticPropertyValue('key'); // works, but is odd -- should we just store the key name on the collection?
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
        $insert = \ClubSpeed\Utility\Params::buildInsert($record);
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
            //      $batch = \ClubSpeed\Utility\Params::buildBatchInsert($records);
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
        $select = \ClubSpeed\Utility\Params::buildSelect($this->definition->newInstance());
        $results = $this->conn->query($select['statement']);
        $all = array();
        foreach($results as $result) {
            $all[] = $this->definition->newInstance($result);
        }
        return $all;
    }

    public function get($id) {
        $get = \ClubSpeed\Utility\Params::buildGet($this->definition->newInstance($id));
        $results = $this->conn->query($get['statement'], $get['values']);
        if (isset($results) && count($results) > 0)
            return $this->definition->newInstance($results[0]);
        return null;
    }

    public function find($data) {
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data;
        else
            $record = $this->definition->newInstance($data);
        $find = \ClubSpeed\Utility\Params::buildFind($record);
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
        $update = \ClubSpeed\Utility\Params::buildUpdate($record);
        $affected = $this->conn->exec($update['statement'], @$update['values']);
        return $affected;
    }

    public function delete($data) {
        if (is_object($data) && $this->definition->isInstance($data))
            $record = $data;
        else
            $record = $this->definition->newInstance($data);
        $delete = \ClubSpeed\Utility\Params::buildDelete($record);
        $affected = $this->conn->exec($delete['statement'], $delete['values']);
        return $affected;
    }

    public function map($type, $params = array()) {
        $mapped = null;
        if (is_array($params) || is_object($params)) {
            $mapped = array();
            switch ($type) {
                case 'client':
                    $map = $this->dbToJson;
                    break;
                case 'server':
                    $map = $this->jsonToDb;
                    break;
            }
            foreach($params as $key => $val) {
                if (isset($map[$key]))
                    $mapped[$map[$key]] = $val;
            }
        }
        else {
            // expected a single name, not multiple params as an associative array -- rename parameter?
            switch($type) {
                case 'client':
                    $mapped = $this->dbToJson[$params];
                    break;
                case 'server':
                    $mapped = $this->jsonToDb[$params];
                    break;
            }
        }
        return $mapped;
    }

    public function compress($data = array()) {
        // default - this should be overwritten if special logic is necessary
        $table = $this->definition->getShortName();
        $compressed = array(
            $table => array()
        );
        $inner =& $compressed[$table];
        if (isset($data) && !is_array($data))
            $data = array($data);
        foreach($data as $record) {
            if (!empty($record))
                $inner[] = $this->map('client', $record);
        }
        return $compressed;
    }
}