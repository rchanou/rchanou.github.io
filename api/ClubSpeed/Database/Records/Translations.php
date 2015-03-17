<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Convert as Convert;

class Translations extends BaseRecord {
    protected static $_definition;

    public $TranslationsID;
    public $Namespace;
    public $Name;
    public $Culture;
    public $DefaultValue;
    public $Value;
    public $Description;
    public $Created;
}