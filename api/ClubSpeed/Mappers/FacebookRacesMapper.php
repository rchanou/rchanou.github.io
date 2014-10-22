<?php

namespace ClubSpeed\Mappers;

class FacebookRacesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'facebook';
        $this->register(array(
              'CustID'         => 'customerId'
            , 'Access_Token'   => 'token'
            , 'HeatNo'         => 'heatId'
            , 'HeatTypeName'   => 'heatType'
            , 'FinishPosition' => ''
            , 'Finish'         => 'heatFinishTime'
        ));
    }
}