<?php

namespace ClubSpeed\Database\Records;

class Version_CS extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $VersionNumber;
    public $CurrentVersion;
    public $UpdatedDate;
}