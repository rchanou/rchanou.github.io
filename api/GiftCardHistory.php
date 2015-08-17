<?php

use ClubSpeed\Enums\Enums as Enums;

class GiftCardHistory extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'giftCardHistory';
    }
}