<?php

namespace ClubSpeed\Database\Collections;

abstract class DbCollection {

    protected $conn;
    protected $definition;

    protected $dbToJson; // expected to be overwritten in the class extension (can't declare properties as abstract in php)
    protected $jsonToDb; // expected to be overwritten in the class extension (can't declare properties as abstract in php)

    // move build statements here?
    // they really don't belong in the connection class
    // maybe in a helper class, if nothing else -- similar to params

    public function __construct(&$CSConnection) {
        $this->conn = $CSConnection;
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