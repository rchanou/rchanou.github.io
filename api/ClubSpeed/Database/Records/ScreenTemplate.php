<?php

namespace ClubSpeed\Database\Records;

class ScreenTemplate extends BaseRecord {
    protected static $_definition;
    
    public $TemplateID;
    public $TemplateName;
    public $ShowScoreboard;
    public $IdleTime;
    public $ScoreBoardTrackNo;
    public $Deleted;
    public $StartPosition;
    public $SizeX;
    public $SizeY;
}