<?php

namespace ClubSpeed\Database\Records;

class Logs extends BaseRecord {

    public static $table = 'dbo.Logs';
    public static $tableAlias = 'lgs';
    public static $key = 'LogID';

    public $LogID;
    public $Message;
    public $LogDate;
    public $TerminalName;
    public $UserName;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['LogID']))          $this->LogID        = \ClubSpeed\Utility\Convert::toNumber          ($data['LogID']);
                    if (isset($data['Message']))        $this->Message      = \ClubSpeed\Utility\Convert::toString          ($data['Message']);
                    if (isset($data['LogDate']))        $this->LogDate      = \ClubSpeed\Utility\Convert::toDateForServer   ($data['LogDate']);
                    if (isset($data['TerminalName']))   $this->TerminalName = \ClubSpeed\Utility\Convert::toString          ($data['TerminalName']);
                    if (isset($data['UserName']))       $this->UserName     = \ClubSpeed\Utility\Convert::toString          ($data['UserName']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}