<?php

use ClubSpeed\Enums\Enums as Enums;

class GiftCardBalance extends BaseApi {
    
    function __construct() {
        parent::__construct();
        $this->mapper               = new \ClubSpeed\Mappers\GiftCardBalanceMapper();
        $this->interface            = $this->logic->giftCardBalance;

        // deny all access other than get by explicit id for security reasons
        // note that get by id should absoltuely stay as API_PRIVATE_ACCESS
        // so any gift card balance lookups must be done through an intermediate,
        // and allowed application with a private key (such as the online booking php)
        $this->access['all']        = Enums::API_NO_ACCESS;
        $this->access['delete']     = Enums::API_NO_ACCESS;
        $this->access['filter']     = Enums::API_NO_ACCESS;
        $this->access['match']      = Enums::API_NO_ACCESS;
        $this->access['post']       = Enums::API_NO_ACCESS;
        $this->access['put']        = Enums::API_NO_ACCESS;
    }
}