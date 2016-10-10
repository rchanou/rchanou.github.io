<?php

namespace ClubSpeed\Mappers;

class EventRoundsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventRounds';
        $this->register(array(
              'EventRound' => 'eventRoundId'
            , 'CadetsPerHeat' => 'cadetsPerHeat'
            , 'EventID' => 'eventId'
            // , 'GridID' => 'gridId'
            , 'HeatDescription' => 'heatDescription'
            , 'HeatsPerRacer' => 'heatsPerRacer'
            , 'HeatTypeNo' => 'heatTypeId'
            // , 'IsInverted' => 'isInverted'
            // , 'IsSemi' => 'isSemi'
            // , 'LineUpType' => 'lineUpType'
            // , 'ProceedMethod' => 'proceedMethod'
            // , 'ProceedValue' => 'proceedValue'
            , 'RacersPerHeat' => 'racersPerHeat'
            , 'RoundNum' => 'roundNumber'
            // , 'RoundWinBy' => 'roundWinBy'
            // , 'ScoreSystemID' => 'scoreSystemId'
        ));
    }
}
