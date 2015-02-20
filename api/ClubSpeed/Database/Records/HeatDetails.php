<?php

namespace ClubSpeed\Database\Records;
use Clubspeed\Utility\Arrays;

class HeatDetails extends BaseRecord {

    public static $table      = 'dbo.HeatDetails';
    public static $tableAlias = 'htdtls';
    public static $key        = array(
        'HeatNo',
        'CustID'
    );

    public $HeatNo;
    public $CustID;
    public $AutoNo;
    public $LineUpPosition;
    public $GroupID;
    public $RPM;
    public $PointHistoryID;
    public $FirstTime;
    public $UserID;
    public $FinishPosition;
    public $GroupFinishPosition;
    public $RPMDiff;
    public $PositionEditedDate;
    public $HistoryAutoNo;
    public $Scores;
    public $TimeAdded;
    public $AssignedtoEntitleHeat;

    public function __construct() {
        call_user_func_array(array($this, 'load'), func_get_args());
    }

    public function load() {
        $args = func_get_args();
        if (count($args) > 0) {
            $data = end($args);
            if (isset($data)) {
                if (Arrays::isAssociative($data)) {
                // if (is_array($data)) {
                    if (!empty($data)) {
                        if (isset($data['HeatNo']))                 $this->HeatNo                = \ClubSpeed\Utility\Convert::toNumber         ($data['HeatNo']);
                        if (isset($data['CustID']))                 $this->CustID                = \ClubSpeed\Utility\Convert::toNumber         ($data['CustID']);
                        if (isset($data['AutoNo']))                 $this->AutoNo                = \ClubSpeed\Utility\Convert::toNumber         ($data['AutoNo']);
                        if (isset($data['LineUpPosition']))         $this->LineUpPosition        = \ClubSpeed\Utility\Convert::toNumber         ($data['LineUpPosition']);
                        if (isset($data['GroupID']))                $this->GroupID               = \ClubSpeed\Utility\Convert::toNumber         ($data['GroupID']);
                        if (isset($data['RPM']))                    $this->RPM                   = \ClubSpeed\Utility\Convert::toNumber         ($data['RPM']);
                        if (isset($data['PointHistoryID']))         $this->PointHistoryID        = \ClubSpeed\Utility\Convert::toNumber         ($data['PointHistoryID']);
                        if (isset($data['FirstTime']))              $this->FirstTime             = \ClubSpeed\Utility\Convert::toBoolean        ($data['FirstTime']);
                        if (isset($data['UserID']))                 $this->UserID                = \ClubSpeed\Utility\Convert::toNumber         ($data['UserID']);
                        if (isset($data['FinishPosition']))         $this->FinishPosition        = \ClubSpeed\Utility\Convert::toNumber         ($data['FinishPosition']);
                        if (isset($data['GroupFinishPosition']))    $this->GroupFinishPosition   = \ClubSpeed\Utility\Convert::toNumber         ($data['GroupFinishPosition']);
                        if (isset($data['RPMDiff']))                $this->RPMDiff               = \ClubSpeed\Utility\Convert::toNumber         ($data['RPMDiff']);
                        if (isset($data['PositionEditedDate']))     $this->PositionEditedDate    = \ClubSpeed\Utility\Convert::toDateForServer  ($data['PositionEditedDate']);
                        if (isset($data['HistoryAutoNo']))          $this->HistoryAutoNo         = \ClubSpeed\Utility\Convert::toNumber         ($data['HistoryAutoNo']);
                        if (isset($data['Scores']))                 $this->Scores                = \ClubSpeed\Utility\Convert::toNumber         ($data['Scores']);
                        if (isset($data['TimeAdded']))              $this->TimeAdded             = \ClubSpeed\Utility\Convert::toString         ($data['TimeAdded']);
                        if (isset($data['AssignedtoEntitleHeat']))  $this->AssignedtoEntitleHeat = \ClubSpeed\Utility\Convert::toBoolean        ($data['AssignedtoEntitleHeat']);
                    }
                }
                else {
                    $keys = array();
                    if (count(self::$key) === count($args))
                        $keys = $args; // keys passed in as Instance(key1, key2)
                    else if (count(self::$key) === count($data))
                        $keys = $data; // keys passed in as Instance(array(key1, key2))
                    else // wrong number of keys passed, throw exception?
                        return;
                        // throw new \CSException("HeatDetails record received the wrong number of primary keys!");
                    $c = count(self::$key);
                    for($i = 0; $i < $c; $i++) {
                        $this->{self::$key[$i]} = $keys[$i];
                    }
                }
            }
        }
    }
}