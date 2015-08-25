<?php

namespace ClubSpeed\Mappers;

class EventHeatDetailsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventHeatDetails';
        $this->register(array(
              'EventID'           => ''
            , 'CustID'            => 'customerId'
            , 'RPM'               => 'proskill'
            , 'DateAdded'         => 'added'
            , 'RoundLoseNum'      => ''
            , 'CheckID'           => ''
            , 'TotalRacesInEvent' => 'totalRaces'
        ));
    }
}
