<?php

namespace ClubSpeed\Database\Records;

class AuthenticationTokens extends BaseRecord {

    public static $table      = 'dbo.AuthenticationTokens';
    public static $tableAlias = 'at';
    public static $key        = 'AuthenticationTokensID';

    public $AuthenticationTokensID;
    public $CustomersID;
    public $RemoteUserID;
    public $TokenType;
    public $Token;
    public $CreatedAt;
    public $ExpiresAt;
    public $Meta;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['AuthenticationTokensID']))     $this->AuthenticationTokensID   = \ClubSpeed\Utility\Convert::toNumber($data['AuthenticationTokensID']);
                    if (isset($data['CustomersID']))                $this->CustomersID              = \ClubSpeed\Utility\Convert::toNumber($data['CustomersID']);
                    if (isset($data['RemoteUserID']))               $this->RemoteUserID             = \ClubSpeed\Utility\Convert::toString($data['RemoteUserID']);
                    if (isset($data['TokenType']))                  $this->TokenType                = \ClubSpeed\Utility\Convert::toString($data['TokenType']);
                    if (isset($data['Token']))                      $this->Token                    = \ClubSpeed\Utility\Convert::toString($data['Token']);
                    if (isset($data['CreatedAt']))                  $this->CreatedAt                = \ClubSpeed\Utility\Convert::toString($data['CreatedAt']);
                    if (isset($data['ExpiresAt']))                  $this->ExpiresAt                = \ClubSpeed\Utility\Convert::toString($data['ExpiresAt']);
                    if (isset($data['Meta']))                       $this->Meta                     = \ClubSpeed\Utility\Convert::toString($data['Meta']);
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
                
        //         break;
        // }
    }
}