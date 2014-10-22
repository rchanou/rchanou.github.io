<?php

use ClubSpeed\Enums\Enums as Enums;

class PrimaryCustomers extends BaseApi {
    
    function __construct() {
        parent::__construct();
        $this->mapper               = new \ClubSpeed\Mappers\PrimaryCustomersMapper();
        $this->interface            = $this->logic->primaryCustomers;
        $this->access['all']        = Enums::API_NO_ACCESS; // deny all access due to large result size
        $this->access['delete']     = Enums::API_NO_ACCESS;
        $this->access['post']       = Enums::API_NO_ACCESS;
        $this->access['put']        = Enums::API_NO_ACCESS;
    }
}