<?php

use ClubSpeed\Enums\Enums as Enums;

class SpeedLevel extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'SpeedLevel';

        $this->access['all']    = Enums::API_PUBLIC_ACCESS;
        $this->access['get']    = Enums::API_PUBLIC_ACCESS;
        $this->access['filter'] = Enums::API_PUBLIC_ACCESS;
    }
}