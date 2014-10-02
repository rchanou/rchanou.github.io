<?php

namespace ClubSpeed\Database\Helpers;

class Comparator {

    public $left;
    public $operator;
    public $right;

    protected static $pattern = '/(<=?|>=?|(?: )IS(?: NOT)?(?: )|<>|!?=|%(?:[N]?EQ|LT[E]?|GT[E]?);)/i'; // note case-insensitivity
    protected static $operators = array(
          '<'      => '<'
        , '<='     => '<='
        , '>'      => '>'
        , '>='     => '>='
        , '='      => '='
        , '!='     => '!='
        , '<>'     => '<>'
        , 'is'     => 'IS'
        , 'is not' => 'IS NOT'
        , '%lt;'   => '<'
        , '%lte;'  => '<='
        , '%gt;'   => '>'
        , '%gte;'  => '>='
        , '%eq;'   => '='
        , '%neq;'  => '!='
    );

    public function __construct($data) {
        if (isset($data) && is_string($data))
            $this->parse($data);
    }

    public function parse($string) {
        $groups = preg_split(self::$pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach($groups as $key => $group) {
            $groups[$key] = trim($group);
        }
        if (count($groups) === 3) {
            $this->left         = $groups[0];
            $this->operator     = @self::$operators[strtolower($groups[1])];
            $this->right        = $groups[2];
        }
        else {
            throw new \CSException("Grouped comparator was unable to parse the provided string! Received: " . $string);
        }
    }

    public function validate() {
        if (!isset($this->left))
            return false;
        if (!isset($this->right))
            return false;
        if (!isset($this->operator))
            return false;
        if (!in_array($this->operator, self::$operators))
            return false;
        return true;
    }
}