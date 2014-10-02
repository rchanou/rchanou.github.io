<?php

namespace ClubSpeed\Database\Records;

class ScreenTemplateDetail extends BaseRecord {

    public static $table      = 'dbo.ScreenTemplateDetail';
    public static $tableAlias = 'std';
    public static $key        = 'ID';
    
    public $ID;
    public $TemplateID;
    public $Seq;
    public $TypeID;
    public $TimeInSecond;
    public $Text0;
    public $Text1;
    public $Text2;
    public $Text3;
    public $Text4;
    public $Text5;
    public $Text6;
    public $Enable;
    public $TrackNo;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['ID']))             $this->ID           = \ClubSpeed\Utility\Convert::toNumber ($data['ID']);
                    if (isset($data['TemplateID']))     $this->TemplateID   = \ClubSpeed\Utility\Convert::toNumber ($data['TemplateID']);
                    if (isset($data['Seq']))            $this->Seq          = \ClubSpeed\Utility\Convert::toNumber ($data['Seq']);
                    if (isset($data['TypeID']))         $this->TypeID       = \ClubSpeed\Utility\Convert::toNumber ($data['TypeID']);
                    if (isset($data['TimeInSecond']))   $this->TimeInSecond = \ClubSpeed\Utility\Convert::toNumber ($data['TimeInSecond']);
                    if (isset($data['Text0']))          $this->Text0        = \ClubSpeed\Utility\Convert::toString ($data['Text0']);
                    if (isset($data['Text1']))          $this->Text1        = \ClubSpeed\Utility\Convert::toString ($data['Text1']);
                    if (isset($data['Text2']))          $this->Text2        = \ClubSpeed\Utility\Convert::toString ($data['Text2']);
                    if (isset($data['Text3']))          $this->Text3        = \ClubSpeed\Utility\Convert::toString ($data['Text3']);
                    if (isset($data['Text4']))          $this->Text4        = \ClubSpeed\Utility\Convert::toString ($data['Text4']);
                    if (isset($data['Text5']))          $this->Text5        = \ClubSpeed\Utility\Convert::toString ($data['Text5']);
                    if (isset($data['Text6']))          $this->Text6        = \ClubSpeed\Utility\Convert::toString ($data['Text6']);
                    if (isset($data['Enable']))         $this->Enable       = \ClubSpeed\Utility\Convert::toBoolean($data['Enable']);
                    if (isset($data['TrackNo']))        $this->TrackNo      = \ClubSpeed\Utility\Convert::toNumber ($data['TrackNo']);
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