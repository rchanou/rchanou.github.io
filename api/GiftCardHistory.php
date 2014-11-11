<?php

use ClubSpeed\Enums\Enums as Enums;

class GiftCardHistory extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\GiftCardHistoryMapper();
        $this->interface        = $this->logic->giftCardHistory;
        $this->access['all']    = Enums::API_NO_ACCESS;
        $this->access['delete'] = Enums::API_NO_ACCESS;
    }
}