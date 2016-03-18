<?php

namespace ClubSpeed\Containers;

require_once(__DIR__.'/BaseContainer.php');
require_once(__DIR__.'/../Database/Helpers/GroupedComparator.php');

class ParamsContainer extends BaseContainer {

    public $select;
    public $params;
    public $filter;

    private static $reserved = array(
        'key'
        , 'debug'
        , 'select'
        , 'skip'
        , 'limit'
        , 'take'
        , 'offset'
        , 'page'
        , 'XDEBUG_PROFILE'
    );

    private static $special = array(
        'filter'
    );

    public function __construct($data = array()) {
        // parent::__construct($data);
        $this->params = array();
        $this->load($data);
    }

    public function load(array $data = array()) {
        if (is_null($data) || empty($data))
            return; // break early, or throw exception?

        foreach($data as $key => $val) {
            // move the limit for insert/update to here? then we have to use json styled-names, instead of database styled-names
            if (
                    is_array($val) // batch -- assume its non-reserved, non-special?
                ||  (
                        !in_array($key, self::$reserved)
                    &&  !in_array($key, self::$special)
                    &&  (
                        !empty($limit)
                            ? in_array($key, $limit)
                            : true
                    )
                )
            ) {
                $this->params[$key] = $val;
            }
        }
        if (isset($data['select'])) {
            foreach(explode(',', $data['select']) as $key => $val) {
                $this->select[$key] = trim($val);
            }
        }
        if (isset($data['filter']))
            $this->filter = new \ClubSpeed\Database\Helpers\GroupedComparator($data['filter']);

    }

    public function isFilter() {
        return isset($this->filter) && $this->filter instanceof \ClubSpeed\Database\Helpers\GroupedComparator;
    }

    public function hasSelect() {
        return isset($this->select) && !empty($this->select);
    }

    public function hasFilter() {
        return isset($this->filter) && !empty($this->filter);
    }

    public function hasParams() {
        return isset($this->params) && !empty($this->params);
    }

    public function isEmpty() {
        return !($this->hasSelect() || $this->hasFilter() || $this->hasParams());
    }
}