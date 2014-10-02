<?php

namespace ClubSpeed\Database\Records;

class ResourceSets extends BaseRecord {

    public static $table      = 'dbo.ResourceSets';
    public static $tableAlias = 'rs';
    public static $key        = 'ResourceID';
    
    public $ResourceID;
    public $ResourceSetName;
    public $Culture;
    public $ResourceName;
    public $ResourceValue;
    public $ResourceType;
    public $ResourceComment;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['ResourceID']))         $this->ResourceID       = \ClubSpeed\Utility\Convert::toNumber ($data['ResourceID']);
                    if (isset($data['ResourceSetName']))    $this->ResourceSetName  = \ClubSpeed\Utility\Convert::toString ($data['ResourceSetName']);
                    if (isset($data['Culture']))            $this->Culture          = \ClubSpeed\Utility\Convert::toString ($data['Culture']);
                    if (isset($data['ResourceName']))       $this->ResourceName     = \ClubSpeed\Utility\Convert::toString ($data['ResourceName']);
                    if (isset($data['ResourceValue']))      $this->ResourceValue    = \ClubSpeed\Utility\Convert::toString ($data['ResourceValue']);
                    if (isset($data['ResourceType']))       $this->ResourceType     = \ClubSpeed\Utility\Convert::toString ($data['ResourceType']);
                    if (isset($data['ResourceComment']))    $this->ResourceComment  = \ClubSpeed\Utility\Convert::toString ($data['ResourceComment']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber(+$data);
            }
        }
    }

    public function validate($type) {
        switch (strtolower($type)) {
            case 'insert':
                if (empty($this->ResourceSetName))
                    throw new \RequiredArgumentMissingException("Create resource set requires a non-empty ResourceSetName!");
                if (empty($this->ResourceName))
                    throw new \RequiredArgumentMissingException("Create resource set requires a non-empty ResourceName!");
                if (is_null($this->ResourceValue))
                    throw new \RequiredArgumentMissingException("Create resource set requires a ResourceValue!");
                if (!strstr($this->ResourceSetName, '.'))
                    throw new \InvalidArgumentValueException("Create resource set requires a ResourceSetName which contains a '.'! ex: 'Namespace.Set'");
                if (substr($this->ResourceName, 0, 3) !== 'str')
                    throw new \InvalidArgumentValueException("Create resource set requires a ResourceName which begins with 'str'! ex: 'strMyResourceName'");
                break;
            case 'update':
                // todo
                break;
        }
    }
}