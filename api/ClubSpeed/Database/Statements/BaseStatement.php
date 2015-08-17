<?php

namespace ClubSpeed\Database\Statements;
use ClubSpeed\Utility\Arrays;

class BaseStatement {

    public $left;
    public $right;
    public $operator;
    protected static $counter = 0;

    public function __construct($args = array()) {
        $this->left     = $args['left'];
        $this->operator = $args['operator'];
        $this->right    = $args['right'];
    }

    public function left($left) {
        $this->left = $left;
        return $this;
    }

    public function operator($operator) {
        $this->operator = $operator;
        return $this;
    }

    public function right($right) {
        $this->right = $right;
        return $this;
    }

    public function build() {
        $statements = array();
        $values = array();
        $statements[] = $this->left;
        $statements[] = $this->operator;
        $param = ':p' . self::$counter++; // number is not important, just that it should be unique per statement
        $statements[] = $param;
        $values[$param] = $this->right;
        $statement = Arrays::join($statements, ' ');
        return array(
            'statement' => $statement,
            'values' => $values
        );
    }
}