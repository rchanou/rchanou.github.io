<?php

class Categories extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'Categories';
    }
}
