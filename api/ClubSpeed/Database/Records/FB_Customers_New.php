<?php

namespace ClubSpeed\Database\Records;

class FB_Customers_New extends BaseRecord {

    public static $table      = 'dbo.FB_Customers_New';
    public static $tableAlias = 'fbcn';
    public static $key        = 'FB_CustId';

    public $FB_CustId;
    public $CustId;
    public $UId;
    public $Access_token;
    public $AllowEmail;
    public $AllowPost;
    public $Enabled;
    
    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['FB_CustId']))      $this->FB_CustId    = \ClubSpeed\Utility\Convert::toNumber      ($data['FB_CustId']);
                    if (isset($data['CustId']))         $this->CustId       = \ClubSpeed\Utility\Convert::toNumber      ($data['CustId']);
                    if (isset($data['UId']))            $this->UId          = \ClubSpeed\Utility\Convert::toString      ($data['UId']);
                    if (isset($data['Access_token']))   $this->Access_token = \ClubSpeed\Utility\Convert::toString      ($data['Access_token']);
                    if (isset($data['AllowEmail']))     $this->AllowEmail   = \ClubSpeed\Utility\Convert::toBoolean     ($data['AllowEmail']);
                    if (isset($data['AllowPost']))      $this->AllowPost    = \ClubSpeed\Utility\Convert::toBoolean     ($data['AllowPost']);
                    if (isset($data['Enabled']))        $this->Enabled      = \ClubSpeed\Utility\Convert::toBoolean     ($data['Enabled']);
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