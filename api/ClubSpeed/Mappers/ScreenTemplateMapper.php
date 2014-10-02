<?php

namespace ClubSpeed\Mappers;

class ScreenTemplateMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'channels';
        $this->register(array(
              'TemplateID'          => 'screenTemplateId'
            , 'TemplateName'        => 'screenTemplateName'
            , 'ShowScoreboard'      => 'showScoreboard'
            , 'IdleTime'            => 'postRaceIdleTime'
            , 'ScoreBoardTrackNo'   => 'trackId'
            , 'Deleted'             => 'deleted'
            , 'StartPosition'       => 'startPosition'
            , 'SizeX'               => 'sizeX'
            , 'SizeY'               => 'sizeY'
        ));
    }
}