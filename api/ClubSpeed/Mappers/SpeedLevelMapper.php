<?php

namespace ClubSpeed\Mappers;

class SpeedLevelMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'speedLevel';
        $this->register(array(
              'SpeedLevel'               => ''
            , 'Description'              => ''
            , 'Deleted'                  => ''
        ));
    }
}