<?php

class DiscountType extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'DiscountType';
    }
}