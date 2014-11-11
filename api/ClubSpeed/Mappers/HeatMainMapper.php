<?php

namespace ClubSpeed\Mappers;

class HeatMainMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'heats';
        $this->register(array(
              'HeatNo'                   => 'heatId'
            , 'TrackNo'                  => 'track'
            , 'ScheduledTime'            => ''
            , 'HeatTypeNo'               => 'type'
            , 'LapsOrMinutes'            => ''
            , 'HeatStatus'               => 'status'
            , 'EventRound'               => ''
            , 'Begining'                 => 'beginning' // almost tempted to leave it as begining..
            , 'Finish'                   => ''
            , 'WinBy'                    => ''
            , 'RaceBy'                   => ''
            , 'ScheduleDuration'         => ''
            , 'PointsNeeded'             => ''
            , 'SpeedLevel'               => ''
            , 'HeatColor'                => ''
            , 'NumberOfReservation'      => ''
            , 'MemberOnly'               => ''
            , 'HeatNotes'                => 'notes'
            , 'ScoreID'                  => ''
            , 'RacersPerHeat'            => ''
            , 'NumberOfCadetReservation' => ''
            , 'CadetsPerHeat'            => ''
        ));
    }
}