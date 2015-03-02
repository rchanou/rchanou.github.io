<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Database\Records\BaseRecord;
use ClubSpeed\Logic\LogicService as Logic;
use ClubSpeed\Mappers\MapperService as Mappers;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Enums\Enums;

class UnitOfWork {

    protected static $reserved = array(
        // 'action'
        // , 'table'
        // , 'table_id'
        // , 'definition'
        // , 'data'
          'select'
        , 'where'
        , 'order'
        , 'limit'
        , 'offset'
        , 'debug'
    );

    public function __construct($data = null) {
        $this->offset = 0;
        $this->limit = Enums::API_DEFAULT_PAGE_SIZE;
        if (!is_null($data) && !empty($data))
            $this->parse($data);
        return $this;
    }

    // flow
    // -> API receives REST request
    // -> make unit of work
    // -> pass to mapper, map from client properties to server properties
    // -> pass to logic, create typed object, run defaults and error throwing
    // -> pass to sql driver / DbService, build and execute statements
    // -> update the uow reference properties as necessary, then return back to mapper
    // -> allow mapper to re-convert property names back to expected client names
    // -> return from API

    public $action;     // create, update, delete, select, get, all (?)
    public $table;      // table -- use a string if possible, convert to definition later
    public $table_id;   // required for update and delete, return from create (and possibly use with create, if we have it ahead of time -- IE customers)
    public $data;       // used by create and update. update should be non destructive for non-provided items.
    public $select;     // for column selection. assume * if empty?
    public $where;      // only available for selects? mass updates? mass deletes?
    public $order;      // only available for selects?
    public $limit;      // only available for selects
    public $offset;     // only available for selects

    // factory-style extension, if we want it.
    public static function build($data = null) {
        return new UnitOfWork($data);
    }

    public function action($action) {
        $this->action = $action;
        return $this;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function table_id($table_id) {
        $this->table_id = $table_id;
        return $this;
    }

    public function select($select) {
        if (is_string($select) && !empty($select)) {
            // check for json representation of an array
            $_select = json_decode($select, true);
            if (!empty($_select))
                $select = $_select;
            else // check for basic comma separated list
                $select = array_map(function($x) { return trim($x); }, explode(",", $select));
        }
        if (is_array($select) && !empty($select)) // check for isAssociative?
            $this->select = $select;
        return $this;
    }

    public function data($data) {
        // note these shenanigans(!!!)
        // restler dumps the query string and the body
        // into the same array, so we have to manually
        // keep track of which is going to be which.
        if (is_array($data) || is_object($data)) {
            foreach($data as $key => $val) {
                if (Arrays::contains(self::$reserved, function($x) use ($key) {
                    return $x === $key;
                })) {
                    unset($data[$key]); // careful, not sure this will work with classes / BaseRecord.
                }
            }
            if (empty($data))
                $data = null; // empty array, after reserved items were taken out?
        }
        $this->data = $data;
        return $this;
    }

    public function limit($limit) {
        $limit = Convert::toNumber($limit);
        if (!is_null($limit))
            $this->limit = $limit;
        return $this;
    }

    public function offset($offset) {
        // 0 should be a valid number here
        $offset = Convert::toNumber($offset);
        if (!is_null($offset))
            $this->offset = $offset;
        return $this;
    }

    public function page($page) {
        // consider page to be an shortcut for setting offset.
        // use a combination of page + limit to determine the actual start point.
        // note that in order for this to work properly, we need to already have limit set.
        // as a result, make sure parse()'s order is limit -> page -> offset
        // (consider offset to have override over page, since offset is more specific)
        $page = Convert::toNumber($page);
        $limit = Convert::toNumber($this->limit); // should already be a number, unless set manually.
        if (!is_null($page) && !is_null($limit))
            $this->offset = $page * $limit;
        return $this;
    }

    public function order($order) {
        if (is_string($order) && !empty($order)) // assume comma delimited string.
            $order = array_map(function($x) { return trim($x); }, explode(",", $order));
        if (is_array($order) && !empty($order)) {
            // $order should be a regular, numerically indexed array at this point.
            $this->order = array();
            foreach($order as $data) {
                if (strpos($data, " ")) {
                    // assume we have "{COLUMN_NAME} {DIRECTION}"
                    $temp = explode(" ", $data);
                    $column = $temp[0];
                    $direction = strtoupper($temp[1]);
                    if ($direction !== 'DESC')
                        $direction = 'ASC';
                    $this->order[$column] = $direction;
                }
                else // assume we just have a column name. default it to ASC
                    $this->order[$data] = 'ASC';
            }
        }
        return $this;
    }

    public function where($where) {
        // make sure json is decodable.
        // make sure all columns being used are legitimate,
        // or allow the sql builder to handle that?
        if (!empty($where)) {
            // potentially possible to get something other than a string here?
            // seems like restler may take json and convert it by default?
            if (is_array($where)) {
                if (Arrays::isAssociative($where))
                    $this->where = $where;
            }
            else if (is_string($where)) {
                $_where = json_decode($where, true); // store in temp variable, so we still have a reference of what the string was.
                if (empty($_where))
                    throw new \InvalidArgumentValueException("API created a UnitOfWork with a WHERE parameter, but was unable to decode the JSON! Received: " . print_r($where, true));
                $this->where = $_where;
            }
        }
        return $this;
    }

    public function parse($data = array()) {
        // extension method to just slap the $request_data in.
        $this->action(@$data['action'] ?: null);
        $this->table(@$data['table'] ?: null);
        $this->select(@$data['select'] ?: null);
        $this->limit(@$data['limit'] ?: null);
        $this->page(@$data['page'] ?: null); // ORDER MATTERS HERE - DO PAGE FIRST
        $this->offset(@$data['offset'] ?: null); // AND OFFSET SECOND
        $this->order(@$data['order'] ?: null);
        $this->where(@$data['where'] ?: null);
        $this->data($data);
        return $this;
    }
}