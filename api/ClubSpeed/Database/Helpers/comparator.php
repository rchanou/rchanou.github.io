<?php

namespace ClubSpeed\Database\Helpers;

class Comparator {

    public $left;
    public $operator;
    public $right;

    // general rundown of the pattern:
    // 1. accept standard symbol operators, don't require spaces on either side
    // 2. accept standard sql keywords, require spaces on both sides
    // 3. accept operator abbreviations starting with a % and ending with a ;
    // 4. accept operator abbreviations starting with a $
    // 5. all letters are case-insensitive
    protected static $pattern = '/((?: )?<=?|>=?|<>|!?=(?: )?|(?: )IS(?: NOT)?|(?:NOT )?LIKE|IN(?: )|(?:(?=.*;)%|(?:(?!.*;)\$))(?:[N]?EQ|LT[E]?|GT[E]?)(?:;?))/i'; // note case-insensitivity
    public static $operators = array(
          '<'           => '<'
        , '<='          => '<='
        , '>'           => '>'
        , '>='          => '>='
        , '='           => '='
        , '!='          => '!='
        , '<>'          => '<>'
        , 'is'          => 'IS'
        , 'is not'      => 'IS NOT'
        , 'like'        => 'LIKE'
        , 'not like'    => 'NOT LIKE'
        , 'in'          => 'IN'
        , '%lt;'        => '<'
        , '%lte;'       => '<='
        , '%gt;'        => '>'
        , '%gte;'       => '>='
        , '%eq;'        => '='
        , '%neq;'       => '!='
        , '$lt'         => '<'
        , '$lte'        => '<='
        , '$gt'         => '>'
        , '$gte'        => '>='
        , '$eq'         => '='
        , '$neq'        => '!='
        , '$is'         => 'IS' // need a way to handle IS and ISNOT from json object format
        , '$isnot'      => 'IS NOT'
        , '$like'       => 'LIKE'
        , '$notlike'    => 'NOT LIKE'
        , '$lk'         => 'LIKE'
        , '$nlk'        => 'NOT LIKE'
        , '$in'         => 'IN'
        , '$has'        => 'LIKE' // special extension which will automatically surround value in %'s
    );

    public function __construct($data) {
        if (isset($data) && is_string($data))
            $this->parse($data);
    }

    public function parse($string) {
        $groups = preg_split(self::$pattern, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach($groups as $key => $group) {
            $groups[$key] = trim($group);
        }
        if (count($groups) === 3) {
            $this->left     = $groups[0];
            $this->operator = @self::$operators[strtolower($groups[1])];
            $this->right    = $groups[2];

            // IN is its own strange beast.. modify into an array now for later usage, or allow sql builder to do it (?)
            if (stristr($this->operator, "IN") !== false && !empty($this->right)) {
                $this->right = explode(',', $this->right);
                $this->right = str_replace('(', '', $this->right);
                $this->right = str_replace(')', '', $this->right);
                $this->right = array_map(function($x) {
                    return trim($x);
                }, $this->right);
            }
        }
        else {
            throw new \CSException("Comparator was unable to parse the provided string! Received: " . $string);
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