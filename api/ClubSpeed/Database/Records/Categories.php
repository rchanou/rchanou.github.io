<?php

namespace ClubSpeed\Database\Records;

class Categories extends BaseRecord {
    protected static $_definition;

    public $CategoryID;
    public $Description;
    public $Enabled;
    public $SEQ;
    public $largeIcon;
    public $Deleted;
    public $IsCombo;
}
