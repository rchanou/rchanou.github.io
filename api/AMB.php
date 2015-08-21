<?php

use ClubSpeed\Enums\Enums as Enums;

class AMB extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'AMB';
        $this->access['all'] = Enums::API_PUBLIC_ACCESS;
        $this->access['get'] = Enums::API_PUBLIC_ACCESS;
    }
}