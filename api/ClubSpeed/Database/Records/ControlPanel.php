<?php

namespace ClubSpeed\Database\Records;

class ControlPanel extends BaseRecord {

    public static $table      = 'dbo.ControlPanel';
    public static $tableAlias = 'cntrlpnl';
    public static $key        = null; // ControlPanel has a composite primary key structure -- no way to handle this at time of writing (9/26/14)
    
    public $TerminalName;
    public $SettingName;
    public $DataType;
    public $DefaultSetting;
    public $SettingValue;
    public $Description;
    public $Fixed;
    public $CreatedDate;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['TerminalName']))       $this->TerminalName   = \ClubSpeed\Utility\Convert::toString          ($data['TerminalName']);
                    if (isset($data['SettingName']))        $this->SettingName    = \ClubSpeed\Utility\Convert::toString          ($data['SettingName']);
                    if (isset($data['DataType']))           $this->DataType       = \ClubSpeed\Utility\Convert::toString          ($data['DataType']);
                    if (isset($data['DefaultSetting']))     $this->DefaultSetting = \ClubSpeed\Utility\Convert::toString          ($data['DefaultSetting']);
                    if (isset($data['SettingValue']))       $this->SettingValue   = \ClubSpeed\Utility\Convert::toString          ($data['SettingValue']);
                    if (isset($data['Description']))        $this->Description    = \ClubSpeed\Utility\Convert::toString          ($data['Description']);
                    if (isset($data['Fixed']))              $this->Fixed          = \ClubSpeed\Utility\Convert::toBoolean         ($data['Fixed']);
                    if (isset($data['CreatedDate']))        $this->CreatedDate    = \ClubSpeed\Utility\Convert::toDateForServer   ($data['CreatedDate']);
                }
            }
            // else {
            //     $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            // }
        }
    }
}