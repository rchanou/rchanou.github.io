<?php

use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Enums\Enums;

class SpeedScreenChannels extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'speedScreenChannels';
        $this->access['all'] = Enums::API_PUBLIC_ACCESS;
        $this->access['filter'] = Enums::API_PUBLIC_ACCESS;
        $this->access['get'] = Enums::API_PUBLIC_ACCESS;
        $this->access['match'] = Enums::API_PUBLIC_ACCESS;
    }
}