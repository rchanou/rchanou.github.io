<?php

namespace ClubSpeed\Database\Records;

class Version_CS extends BaseRecord {

    public static $table      = 'dbo.Version_CS';
    public static $tableAlias = 'vrsn';
    public static $key        = 'ID';

    public $ID;
    public $VersionNumber;
    public $CurrentVersion;
    public $UpdatedDate;
    
    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['ID']))             $this->ID             = \ClubSpeed\Utility\Convert::toNumber        ($data['ID']);
                    if (isset($data['VersionNumber']))  $this->VersionNumber  = \ClubSpeed\Utility\Convert::toString        ($data['VersionNumber']);
                    if (isset($data['CurrentVersion'])) $this->CurrentVersion = \ClubSpeed\Utility\Convert::toString        ($data['CurrentVersion']);
                    if (isset($data['UpdatedDate']))    $this->UpdatedDate    = \ClubSpeed\Utility\Convert::toDateForServer ($data['UpdatedDate']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        // todo
    }
}