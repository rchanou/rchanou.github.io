<?php

namespace ClubSpeed\Mappers;

class AMBMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'amb';
        $this->register(array(
              'AMBID'          => 'ambId'
            , 'AMBNumber'      => 'number'
            , 'AMBDescription' => 'description'
            , 'AutoNo'         => 'autoNo'
        ));
    }
}