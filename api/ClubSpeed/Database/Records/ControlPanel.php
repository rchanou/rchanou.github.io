<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Arrays;

class ControlPanel extends BaseRecord {

    public static $table      = 'dbo.ControlPanel';
    public static $tableAlias = 'cntrlpnl';
    public static $key        = array(
        'TerminalName', // ORDER IS VERY IMPORTANT HERE
        'SettingName'
    ); // ControlPanel has a composite primary key structure -- no way to handle this at time of writing (9/26/14)
    
    public $TerminalName;
    public $SettingName;
    public $DataType;
    public $DefaultSetting;
    public $SettingValue;
    public $Description;
    public $Fixed;
    public $CreatedDate;

    public function __construct() {
        call_user_func_array(array($this, 'load'), func_get_args());
    }

    public function load() {
        $args = func_get_args();
        if (count($args) > 0) {
            $data = end($args);
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
                else {
                    if (count(self::$key) !== count($args)) // wrong number of keys passed, throw exception?
                        throw new \CSException("ControlPanel record received the wrong number of primary keys!");
                    $c = count(self::$key);
                    for($i = 0; $i < $c; $i++) {
                        $this->{self::$key[$i]} = $args[$i];
                    }
                }
            }
        }
    }
}