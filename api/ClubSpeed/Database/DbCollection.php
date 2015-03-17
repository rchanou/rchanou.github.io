<?php

namespace ClubSpeed\Database;
use ClubSpeed\Database\Helpers\SqlBuilder; // we will want to inject this if we ever want to use something other than Sql Server.
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;

class DbCollection {

    protected $conn;
    public $reflection;
    public $definition;

    public $keys;
    public $table;

    public function __construct(&$conn, &$record) {
        $this->conn = $conn;
        $this->reflection = new \ReflectionClass($record);
        $instance = $this->reflection->newInstance(); // not the most efficient thing in the world, but it's not bad.
        $this->definition = $instance->definition();
        $this->keys = $this->definition['keys'];
        $this->table = $this->definition['table']['name'];
    }

    public function blank() {
        return $this->reflection->newInstance();
    }

    public function dummy($params = array()) {
        return $this->reflection->newInstance($params);
    }

    public function build($data = array()) {
        return $this->reflection->newInstance($data);
    }

    public function create($data) {
        if (is_object($data) && $this->reflection->isInstance($data))
            $record = $data;
        else
            $record = $this->reflection->newInstance($data);
        $insert = \ClubSpeed\Database\Helpers\SqlBuilder::buildInsert($record);
        $lastId = $this->conn->exec($insert['statement'], $insert['values']);
        $lastId = \ClubSpeed\Utility\Convert::toNumber($lastId);
        return $lastId;
    }

    public function uow(&$uow) {
        $sw = $GLOBALS['sw'];
        $this->validate($uow); // ensure the columns being used are legitimate before running any sql.
        $uow->table = $this->reflection; // inject the reflected class as the table
        $uow->definition = $this->definition; // inject the proper definition into the uow
        $query = SqlBuilder::uow($uow); // ~ 8ms to make it all the way to this point from
        switch($uow->action) {
            case 'create':
                $lastId = $this->conn->exec($query['statement'], $query['values']);
                $lastId = \ClubSpeed\Utility\Convert::toNumber($lastId);
                $uow->data->load($lastId); // note that this might not match exactly what is in the database. careful(!!!)
                $uow->table_id = $lastId;  // the equality will depend on whether we have defaults being set by the database.
                return $uow;
            case 'all':
                $results = $this->conn->query($query['statement'], $query['values']);
                $all = array();
                // $sw->push('instance conversion');
                foreach($results as $result)
                    $all[] = $this->reflection->newInstance($result);
                // $sw->pop();
                $uow->data = $all;
                return $uow;
            case 'get':
                $results = $this->conn->query($query['statement'], $query['values']);
                // don't bother running an exists query separately.
                // just use $results to determine whether or not to throw RecordNotFoundException
                if (count($results) < 1) {
                    $ids = (is_array($uow->table_id) ? implode(", ", $uow->table_id) : $uow->table_id);
                    throw new \RecordNotFoundException($this->table . " (" . $ids . ")");
                }
                $get = (count($results) > 0 ? $this->reflection->newInstance(Arrays::first($results)) : null);
                $uow->data = $get;
                return $uow;
            case 'count':
                $results = $this->conn->query($query['statement'], $query['values']);
                $count = 0;
                if (count($results) > 0) {
                    $temp = Arrays::first($results);
                    $count = Convert::toNumber($temp['Count']);
                }
                $uow->data = $count;
                return $uow;
            case 'exists':
                $results = $this->conn->query($query['statement'], $query['values']);
                $exists = false;
                if (count($results) > 0) {
                    $temp = Arrays::first($results);
                    $exists = Convert::toBoolean($temp['Exists']);
                }
                $uow->data = $exists;
                return $uow;
            case 'update':
                // hijack before the count, and run exists before even executing the update
                // performance issue? tests show that the queries take ~0-1ms
                $cloned = clone $uow; // make sure we use a clone, so we don't lose the original data on accident.
                $cloned->action('exists');
                $exists = $this->uow($cloned);
                if (!$exists->data) {
                    $ids = (is_array($uow->table_id) ? implode(", ", $uow->table_id) : $uow->table_id);
                    throw new \RecordNotFoundException($this->table . " (" . $ids . ")");
                }
                $affected = $this->conn->exec($query['statement'], $query['values']);
                // store $affected?
                return $uow;
            case 'delete':
                $cloned = clone $uow; // make sure we use a clone, so we don't lose the original data on accident.
                $cloned->action('exists');
                $exists = $this->uow($cloned);
                if (!$exists->data) {
                    $ids = (is_array($uow->table_id) ? implode(", ", $uow->table_id) : $uow->table_id);
                    throw new \RecordNotFoundException($this->table . " (" . $ids . ")");
                }
                $affected = $this->conn->exec($query['statement'], $query['values']);
                // store $affected?
                return $uow;
            default:
                throw new \InvalidArgumentValueException("DbCollection received a UnitOfWork with an unrecognized action! Received: " . $uow->action);
        }
    }

    public function batchCreate($data) {
        if (is_array($data) && !empty($data)) {
            $records = array();
            foreach($data as $key => $record) {
                $records[$key] = (is_object($record) && $this->reflection->isInstance($record) ? $record : $this->reflection->newInstance($record));
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
        $select = \ClubSpeed\Database\Helpers\SqlBuilder::buildSelect($this->reflection->newInstance());
        $results = $this->conn->query($select['statement']);
        $all = array();
        foreach($results as $result) {
            $all[] = $this->reflection->newInstance($result);
        }
        return $all;
    }

    public function get() {
        $ids = func_get_args();
        $instance = $this->reflection->newInstanceArgs($ids);
        $get = \ClubSpeed\Database\Helpers\SqlBuilder::buildGet($instance);
        $results = $this->conn->query($get['statement'], $get['values']);
        $get = array();
        foreach($results as $result) {
            $get[] = $this->reflection->newInstance($result);
        }
        if (empty($get))
            return null;
        return $get;
        // if (isset($results) && count($results) > 0)
        //     return $this->reflection->newInstance($results[0]);
        // return null;
    }

    public function match($data) {
        if (is_object($data) && $this->reflection->isInstance($data))
            $record = $data;
        else
            $record = $this->reflection->newInstance($data);
        $match = \ClubSpeed\Database\Helpers\SqlBuilder::buildFind($record); // note we are using buildFind at this point
        $results = $this->conn->query($match['statement'], @$match['values']);
        $match = array();
        foreach($results as $result) {
            $match[] = $this->reflection->newInstance($result);
        }
        return $match;
    }

    public function find($comparators = array()) {
        if (!$comparators instanceof \ClubSpeed\Database\Helpers\GroupedComparator)
            $comparators = new \ClubSpeed\Database\Helpers\GroupedComparator($comparators);
        if (!$this->validateComparators($comparators))
            throw new \CSException("Unable to validate querystring comparators! Check the syntax of the filter querystring.");
        $find = \ClubSpeed\Database\Helpers\SqlBuilder::buildFind($this->reflection->newInstance(), $comparators);
        $results = $this->conn->query($find['statement'], @$find['values']);
        $find = array();
        foreach($results as $result) {
            $find[] = $this->reflection->newInstance($result);
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
            $query[] = $this->reflection->newInstance($result);
        }
        return $query;
    }

    public function update($data) {
        if (is_object($data) && $this->reflection->isInstance($data))
            $record = $data;
        else
            $record = $this->reflection->newInstance($data);
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
        if (is_object($data) && $this->reflection->isInstance($data))
            $record = $data;
        // else if (is_array($data))
            // $record = $this->reflection->newInstance($data);
        else
            $record = $this->reflection->newInstanceArgs($args);
        $delete = \ClubSpeed\Database\Helpers\SqlBuilder::buildDelete($record);
        $affected = $this->conn->exec($delete['statement'], $delete['values']);
        // return $affected;
    }

    public function exists() {
        $args = func_get_args();
        $data = reset($args);
        if (is_object($data) && $this->reflection->isInstance($data))
            $record = $data; // if the first arg is already the right instance, just use it
        else
            $record = $this->reflection->newInstanceArgs($args); // otherwise, pass the stuff on -- note that this may be multiple ids (hence, the arg issues)
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

    public function validate(&$uow) {
        // items to handle (note that while we are using mapper, this shouldn't be necessary for client validation.)
        // 1. select
        // 2. where
        // 3. order
        // 4. data(?) -- may not be necessary, since we are 'casting' to properly 'typed' objects.
        if (!empty($uow->select)) {
            if (!is_array($uow->select))
                throw new \CSException('Received a UnitOfWork with a select in an invalid format! Expected array, received: ' . print_r($uow->select, true));
            foreach($uow->select as $select) {
                if (!$this->reflection->hasProperty($select))
                    throw new \CSException("Received a UnitOfWork with a select and a column not in the table definition! Attempted to use: [" . $this->table . "].[" . $select . "]");
            }
        }
        if (!empty($uow->where)) {
            if (!is_array($uow->where) && !$uow->where instanceof \ClubSpeed\Database\Records\BaseRecord)
                throw new \CSException('Received a UnitOfWork with a where in an invalid format! Expected associative array or BaseRecord, received: ' . print_r($uow->where, true));
            foreach($uow->where as $key => $val) {
                if (!$this->reflection->hasProperty($key))
                    throw new \CSException("Received a UnitOfWork with a where and a column not in the table definition! Attempted to use: [" . $this->table . "].[" . $key . "]");
                // todo: run any necessary conversions on $where, since we can't convert them with the BaseRecord structure
            }
        }
        if (!empty($uow->order)) {
            if (!is_array($uow->order))
                throw new \CSException('Received a UnitOfWork with an order in an invalid format! Expected associative array, received: ' . print_r($uow->order, true));
            foreach($uow->order as $key => $val) {
                if (!$this->reflection->hasProperty($key))
                    throw new \CSException("Received a UnitOfWork with an order and a column not in the table definition! Attempted to use: [" . $this->table . "].[" . $key . "]");
            }
        }
    }

    public function validateComparators($comparators) {
        if (!$comparators->validate())// validate structure
            throw new \CSException('Unable to validate comparator structure!');
            // return false;
        foreach($comparators->comparators as $key => $val) { // validate column names
            // at least one of the filter items must be a column name
            // allow both to be column names?
            if (!$this->reflection->hasProperty($val['comparator']->left) && !$this->reflection->hasProperty($val['comparator']->right))
                return false;
        }
        return true;
    }
}
