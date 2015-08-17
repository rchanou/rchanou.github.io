<?php

namespace ClubSpeed\Database\Statements;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Database\Helpers\Comparator;

class HasStatement extends BaseStatement {

    public function __construct($args = array()) {
        $this->left     = $args['left'];
        $this->operator = Comparator::$operators['$like']; // swap to the like parameter
        $this->right    = $args['right'];
    }

    public function build() {
        $statements = array();
        $values = array();
        $statements[] = $this->left;
        $statements[] = $this->operator;
        $param = ':p' . self::$counter++; // number is not important, just that it should be unique per statement
        $statements[] = $param;
        $statement = Arrays::join($statements, ' ');
        $value = $this->right;
        if (substr($value, 1) !== '%')
            $value = '%' . $value;
        if (substr($value, -1) !== '%')
            $value .= '%';
        $values[$param] = $value;
        return array(
            'statement' => $statement,
            'values'    => $values
        );
    }
}