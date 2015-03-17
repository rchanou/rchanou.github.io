<?php

namespace ClubSpeed\Database\Records;

class ActiveRaceLapCount_V extends BaseRecord {
    protected static $_definition; // must be declared, so BaseRecord can use it in definition()

    public $TrackNo;
    public $HeatNo;
    public $LapCount;
}