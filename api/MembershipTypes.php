<?php

class MembershipTypes extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'MembershipTypes';
    }
}