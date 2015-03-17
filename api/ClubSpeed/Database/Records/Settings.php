<?php

namespace ClubSpeed\Database\Records;

class Settings extends BaseRecord {
    protected static $_definition;

    public $SettingsID;
    public $Namespace;
    public $Name;
    public $Type;
    public $DefaultValue;
    public $Value;
    public $Description;
    public $Created;
    public $IsPublic;
}