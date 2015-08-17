<?php

namespace ClubSpeed\Database\Statements;
use ClubSpeed\Utility\Arrays;

class InStatement extends BaseStatement {

    public function __construct($args = array()) {
        $this->left     = $args['left'];
        $this->operator = $args['operator'];
        $this->right    = $args['right'];
    }

    public function build() {
        $arr = $this->right;
        if (!is_array($arr))
            $arr = array($arr); // or should we throw exception for improper format?
        $statements = array();
        $params = array();
        $values = array();
        foreach($arr as $val) {
            $param = ':p' . self::$counter++;
            $params[] = $param;
            $values[$param] = $val;
        }
        $statements[] = $this->left;
        $statements[] = $this->operator;
        $statements[] = '(' . Arrays::join($params, ', ') . ')';
        $statement    = Arrays::join($statements, ' ');
        return array(
            'statement' => $statement,
            'values'    => $values // values should stay empty, can't make NULL a parameter
        );
    }
}