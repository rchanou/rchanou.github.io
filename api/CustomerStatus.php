<?php

class CustomerStatus extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'CustomerStatus';
    }
}