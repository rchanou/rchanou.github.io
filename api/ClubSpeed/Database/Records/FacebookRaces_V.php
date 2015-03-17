<?php

namespace ClubSpeed\Database\Records;

class FacebookRaces_V extends BaseRecord {
    protected static $_definition;

    public $CustID;
    public $Access_Token;
    public $HeatNo;
    public $HeatTypeName;
    public $FinishPosition;
    public $Finish;
}