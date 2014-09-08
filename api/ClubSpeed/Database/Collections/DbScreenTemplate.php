<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/ScreenTemplate.php');

class DbScreenTemplate extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\ScreenTemplate');
        $this->dbToJson = array(
              'TemplateID'          => 'screenTemplateId'
            , 'TemplateName'        => 'screenTemplateName'
            , 'ShowScoreboard'      => 'showScoreboard'
            , 'IdleTime'            => 'idleTime'
            , 'ScoreBoardTrackNo'   => 'scoreBoardTrackNo'
            , 'Deleted'             => 'deleted'
            , 'StartPosition'       => 'startPosition'
            , 'SizeX'               => 'sizeX'
            , 'SizeY'               => 'sizeY'
        );
        parent::secondaryInit();
    }
}