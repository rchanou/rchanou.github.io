<?php

use Clubspeed\Enums\Enums;

class ActiveRaceLapCount extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'activeRaceLapCount';

        $this->access['all']        = Enums::API_PUBLIC_ACCESS;
        $this->access['get']        = Enums::API_PUBLIC_ACCESS;
        $this->access['delete']     = Enums::API_NO_ACCESS;
        $this->access['create']     = Enums::API_NO_ACCESS;
        $this->access['put']        = Enums::API_NO_ACCESS;
    }
}