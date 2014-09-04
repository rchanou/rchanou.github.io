<?php

namespace ClubSpeed\Database\Records;

require_once(__DIR__.'/DbRecord.php');

class AuthenticationTokens extends DbRecord {

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
                    if (isset($data['RemoteUserID']))               $this->RemoteUserID             = \ClubSpeed\Utility\Convert::toNumber($data['RemoteUserID']);
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

    // protected function _convert() {
    //     $this->AuthenticationTokensID   = \ClubSpeed\Utility\Convert::toNumber($this->AuthenticationTokensID);
    //     $this->CustomersID              = \ClubSpeed\Utility\Convert::toNumber($this->CustomersID);
    //     $this->RemoteUserID             = \ClubSpeed\Utility\Convert::toNumber($this->RemoteUserID);
    //     $this->TokenType                = \ClubSpeed\Utility\Convert::toString($this->TokenType);
    //     $this->Token                    = \ClubSpeed\Utility\Convert::toString($this->Token);
    //     $this->CreatedAt                = \ClubSpeed\Utility\Convert::toString($this->CreatedAt);
    //     $this->ExpiresAt                = \ClubSpeed\Utility\Convert::toString($this->ExpiresAt);
    //     $this->Meta                     = \ClubSpeed\Utility\Convert::toString($this->Meta);
    // }

    // public function toJson() {
    //     return array(
    //           'authenticationTokensID'  => $this->AuthenticationTokensID
    //         , 'customersID'             => $this->CustomersID
    //         , 'remoteUserID'            => $this->RemoteUserID
    //         , 'tokenType'               => $this->TokenType
    //         , 'token'                   => $this->Token
    //         , 'createdAt'               => $this->CreatedAt
    //         , 'expiresAt'               => $this->ExpiresAt
    //         , 'meta'                    => $this->Meta
    //     );
    // }

    public function validate($type) {
        // switch (strtolower($type)) {
        //     case 'insert':
                
        //         break;
        // }
    }
}