<?php

namespace ClubSpeed\Mappers;

class HeatTypesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'heatTypes';
        $this->register(array(
              'HeatTypeNo'             => 'heatTypesId'
            , 'TrackNo'                => 'trackId'
            , 'HeatTypeName'           => 'name'
            , 'WinBy'                  => ''
            , 'RaceBy'                 => ''
            , 'LapsOrMinutes'          => ''
            , 'CannotBelow'            => ''
            , 'CannotExceed'           => ''
            , 'RacersPerHeat'          => ''
            , 'ScheduleDuration'       => ''
            , 'IsEventHeatOnly'        => ''
            , 'SpeedLevel'             => ''
            , 'IsPracticeHeat'         => ''
            , 'Enabled'                => ''
            , 'Deleted'                => ''
            , 'Web'                    => ''
            , 'MemberOnly'             => ''
            , 'OnHeatStart'            => ''
            , 'OnHeatFinishAssignLoop' => ''
            , 'OnHeatStop'             => ''
            , 'Cost'                   => ''
            , 'PrintResult'            => ''
            , 'EntitleHeat'            => ''
            , 'CadetsPerHeat'          => ''
        ));
    }
}