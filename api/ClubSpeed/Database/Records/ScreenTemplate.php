<?php

namespace ClubSpeed\Database\Records;

require_once(__DIR__.'/DbRecord.php');

class ScreenTemplate extends DbRecord {

    public static $table      = 'dbo.ScreenTemplate';
    public static $tableAlias = 'st';
    public static $key        = 'TemplateID';
    
    public $TemplateID;
    public $TemplateName;
    public $ShowScoreboard;
    public $IdleTime;
    public $ScoreBoardTrackNo;
    public $Deleted;
    public $StartPosition;
    public $SizeX;
    public $SizeY;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['TemplateID']))         $this->TemplateID           = \ClubSpeed\Utility\Convert::toNumber ($data['TemplateID']);
                    if (isset($data['TemplateName']))       $this->TemplateName         = \ClubSpeed\Utility\Convert::toString ($data['TemplateName']);
                    if (isset($data['ShowScoreboard']))     $this->ShowScoreboard       = \ClubSpeed\Utility\Convert::toBoolean($data['ShowScoreboard']);
                    if (isset($data['IdleTime']))           $this->IdleTime             = \ClubSpeed\Utility\Convert::toNumber ($data['IdleTime']);
                    if (isset($data['ScoreBoardTrackNo']))  $this->ScoreBoardTrackNo    = \ClubSpeed\Utility\Convert::toNumber ($data['ScoreBoardTrackNo']);
                    if (isset($data['Deleted']))            $this->Deleted              = \ClubSpeed\Utility\Convert::toBoolean($data['Deleted']);
                    if (isset($data['StartPosition']))      $this->StartPosition        = \ClubSpeed\Utility\Convert::toNumber($data['StartPosition']);
                    if (isset($data['SizeX']))              $this->SizeX                = \ClubSpeed\Utility\Convert::toNumber($data['SizeX']);
                    if (isset($data['SizeY']))              $this->SizeY                = \ClubSpeed\Utility\Convert::toNumber($data['SizeY']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        // switch (strtolower($type)) {
        //     case 'insert':
        //         if (!isset($this->HeatMainID))
        //             throw new \InvalidArgumentException("Create online booking requires a HeatMainID!");
        //         if (!isset($this->ProductsID))
        //             throw new \InvalidArgumentException("Create online booking requires a ProductsID!");
        //         if (!isset($this->QuantityTotal) || !is_int($this->QuantityTotal) || $this->QuantityTotal < 1)
        //             throw new \InvalidArgumentException("Create online booking requires a total quantity greater than 0! Received: " . $this->QuantityTotal);
        //         break;
        // }
    }
}