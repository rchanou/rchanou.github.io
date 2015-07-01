<?php

namespace ClubSpeed\Database\Records;

class Sources extends BaseRecord {
    protected static $_definition;

    public $SourceID;
    public $SourceName;
    public $Enabled;
    public $Seq;
    public $Deleted;
    public $CaboOnly;
    public $Languages;
    public $LocationID;
}