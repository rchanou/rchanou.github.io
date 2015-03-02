<?php

class UserTasks extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'userTasks';
    }
}