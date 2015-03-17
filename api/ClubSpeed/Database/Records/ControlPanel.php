<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Arrays;

class ControlPanel extends BaseRecord {
    protected static $_definition;

    public $TerminalName;
    public $SettingName;
    public $DataType;
    public $DefaultSetting;
    public $SettingValue;
    public $Description;
    public $Fixed;
    public $CreatedDate;
}