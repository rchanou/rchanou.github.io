<?php

namespace ClubSpeed\Mappers;

class EventHeatTypesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventHeatTypes';
        $this->register(array(
              'EventHeatTypeNo' => 'eventHeatTypeId'
            , 'CadetsPerHeat' => 'cadetsPerHeat'
            , 'Description' => 'description'
            , 'EventTypeID' => 'eventTypeId'
            // , 'GridID' => 'gridId'
            , 'HeatsPerRacer' => 'heatsPerRacer'
            , 'HeatTypeNo' => 'heatTypeId'
            // , 'IsInverted' => 'isInverted'
            // , 'IsSemi' => 'isSemi'
            // , 'LineUpType' => 'lineUpType'
            // , 'ProceedMethod' => 'proceedMethod'
            // , 'ProceedValue' => 'proceedValue'
            , 'RacersPerHeat' => 'racersPerHeat'
            , 'RoundNum' => 'roundNumber'
            // , 'RoundWinby' => 'roundWinby'
            // , 'ScoreSystemID' => 'scoreSystemId'
            // , 'TeamGroupingType' => 'teamGroupingType'
        ));
    }
}
