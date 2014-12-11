<?php

use ClubSpeed\Enums\Enums as Enums;

class PointHistory extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\PointHistoryMapper();
        $this->interface        = $this->logic->pointHistory;

        // $this->access['post']   = Enums::API_NO_ACCESS;
        // $this->access['put']    = Enums::API_NO_ACCESS;
        // $this->access['delete'] = Enums::API_NO_ACCESS;
    }
}