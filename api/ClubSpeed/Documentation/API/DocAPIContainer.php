<?php

namespace ClubSpeed\Documentation\API;

class DocAPIContainer {

    private $_lazy;

    public function __construct() {
        $this->_lazy = array(); // necessary? we always want everything
    }

    public static function getData() {
        $data = array(
            'sections' => array()
        );
        $data['sections'][] = new DocQueryOperations();
        $data['sections'][] = new DocChecks();
        $data['sections'][] = new DocCheckDetails();
        $data['sections'][] = new DocCheckTotals();
        $data['sections'][] = new DocScreenTemplate();
        return $data;
    }
}