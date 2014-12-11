<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Convert as Convert;

class Translations extends BaseRecord {

    public static $table      = 'dbo.Translations';
    public static $tableAlias = 'trnsltns';
    public static $key        = 'TranslationsID';

    public $TranslationsID;
    public $Namespace;
    public $Name;
    public $Culture;
    public $DefaultValue;
    public $Value;
    public $Description;
    public $Created;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['TranslationsID'])) $this->TranslationsID = Convert::toNumber        ($data['TranslationsID']);
                    if (isset($data['Namespace']))      $this->Namespace      = Convert::toString        ($data['Namespace']);
                    if (isset($data['Name']))           $this->Name           = Convert::toString        ($data['Name']);
                    if (isset($data['Culture']))        $this->Culture        = Convert::toString        ($data['Culture']);
                    if (isset($data['DefaultValue']))   $this->DefaultValue   = Convert::toString        ($data['DefaultValue']);
                    if (isset($data['Value']))          $this->Value          = Convert::toString        ($data['Value']);
                    if (isset($data['Description']))    $this->Description    = Convert::toString        ($data['Description']);
                    if (isset($data['Created']))        $this->Created        = Convert::toDateForServer ($data['Created']);
                }
            }
            else {
                $this->{self::$key} = Convert::toNumber(+$data);
            }
        }
    }
}