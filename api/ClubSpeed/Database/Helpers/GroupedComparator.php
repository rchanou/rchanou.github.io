<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Database\Helpers\Comparator;
use ClubSpeed\Database\Records\BaseRecord;
use ClubSpeed\Utility\Objects;

class GroupedComparator { // could implement iterator aggregate, but we need to modify the values during a foreach for JSON map functionality

    public $comparators = array();

    protected static $pattern = '/ (AND|OR) /i'; // handle parentheses? 
    protected static $connectors = array(
          'AND' => 'AND'
        , 'OR'  => 'OR'
    );

    public function __construct($data = null) {
        if (isset($data)) {
            if (is_string($data))
                $this->parse($data);
            else if ($data instanceof BaseRecord)
                $this->load($data);
        }
    }

    public function parse($string) {
        $this->comparators = array(); // reset each time parse is called (?)
        $groups = preg_split(self::$pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach($groups as $key => $group) {
            $groups[$key] = trim($group);
        }
        // group count should be positive and odd
        // even numbers should be comparators
        // odd numbers should be connectors
        if (count($groups) > 0) {
            $i = 0;
            $this->comparators[] = array(
                'comparator' => new Comparator($groups[$i])
            );
            $i+=1;
            for (; $i < (count($groups)); $i+=2) {
                $this->comparators[] = array(
                    'connector'     => @self::$connectors[@$groups[$i]] // will hit an undefined offset at the end -- move to outside of loop?
                    , 'comparator'  => new Comparator($groups[$i+1])
                );
            }
        }
    }

    public function load($record) {
        if (!Objects::isEmpty($record)) {
            $strings = array();
            foreach($record as $key => $val) { // build a string, then parse it, or just build in-line?
                // building string for now, for simplicity's sake
                if (isset($record->$key))
                    $strings[] = $key . ' = ' . $val;
            }
            $string = implode(' AND ', $strings);
            $this->parse($string);
        }
    }

    public function isEmpty() {
        return count($this->comparators) <= 0;
    }

    public function validate() {
        // TODO: VALIDATE STRUCTURE
        foreach($this->comparators as $key => $val) {
            if (!isset($val['comparator']) || !$val['comparator'] instanceof Comparator)
                return false;
            if (!$val['comparator']->validate())
                return false;
            // check all connectors but last instance (since the last one won't have a connector)?
        }
        return true;
    }
}