<?php

namespace ClubSpeed\Database\Records;

class Settings extends BaseRecord {

    public static $table      = 'dbo.Settings';
    public static $tableAlias = 'settings';
    public static $key        = 'SettingsID';

    public $SettingsID;
    public $Namespace;
    public $Name;
    public $Type;
    public $DefaultValue;
    public $Value;
    public $Description;
    public $Created;
    public $IsPublic;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['SettingsID']))     $this->SettingsID       = \ClubSpeed\Utility\Convert::toNumber          ($data['SettingsID']);
                    if (isset($data['Namespace']))      $this->Namespace        = \ClubSpeed\Utility\Convert::toString          ($data['Namespace']);
                    if (isset($data['Name']))           $this->Name             = \ClubSpeed\Utility\Convert::toString          ($data['Name']);
                    if (isset($data['Type']))           $this->Type             = \ClubSpeed\Utility\Convert::toString          ($data['Type']);
                    if (isset($data['DefaultValue']))   $this->DefaultValue     = \ClubSpeed\Utility\Convert::toString          ($data['DefaultValue']);
                    if (isset($data['Value']))          $this->Value            = \ClubSpeed\Utility\Convert::toString          ($data['Value']);
                    if (isset($data['Description']))    $this->Description      = \ClubSpeed\Utility\Convert::toString          ($data['Description']);
                    if (isset($data['Created']))        $this->Created          = \ClubSpeed\Utility\Convert::toDateForServer   ($data['Created']);
                    if (isset($data['IsPublic']))       $this->IsPublic         = \ClubSpeed\Utility\Convert::toBoolean         ($data['IsPublic']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}