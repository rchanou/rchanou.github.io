<?php

namespace ClubSpeed\Database\Collections;

class DbScreenTemplate extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\ScreenTemplate');
        parent::__construct($db);
        // $this->dbToJson = array(
        //       'TemplateID'          => 'screenTemplateId'
        //     , 'TemplateName'        => 'screenTemplateName'
        //     , 'ShowScoreboard'      => 'showScoreboard'
        //     , 'IdleTime'            => 'idleTime'
        //     , 'ScoreBoardTrackNo'   => 'scoreBoardTrackNo'
        //     , 'Deleted'             => 'deleted'
        //     , 'StartPosition'       => 'startPosition'
        //     , 'SizeX'               => 'sizeX'
        //     , 'SizeY'               => 'sizeY'
        // );
        // parent::secondaryInit();
    }
}