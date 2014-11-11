<?php

use ClubSpeed\Enums\Enums as Enums;

class Payments extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\PaymentsMapper();
        $this->interface        = $this->logic->payment;
        $this->access['all']    = Enums::API_NO_ACCESS;
        $this->access['delete'] = Enums::API_NO_ACCESS;
    }
}