<?php

namespace ClubSpeed\Mappers;

class ActiveRaceLapCountMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'races';
        $this->register(array(
              'TrackNo'         => 'trackId'
            , 'HeatNo'          => 'heatId'
            , 'LapCount'        => ''
        ));
    }
}