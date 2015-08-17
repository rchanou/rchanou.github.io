<?php

namespace ClubSpeed\Database\Statements;
use ClubSpeed\Utility\Arrays;

class IsStatement extends BaseStatement {

    public function __construct($args = array()) {
        $this->left     = $args['left'];
        $this->operator = $args['operator'];
        $this->right    = 'NULL'; // the IS and IS NOT operators only work with 'NULL' in sql
    }

    public function build() {
        $statements   = array();
        $values       = array();
        $statements[] = $this->left;
        $statements[] = $this->operator;
        $statements[] = 'NULL'; // or $this->right
        $statement    = Arrays::join($statements, ' ');
        return array(
            'statement' => $statement,
            'values'    => $values // values should stay empty, can't make NULL a parameter
        );
    }
}