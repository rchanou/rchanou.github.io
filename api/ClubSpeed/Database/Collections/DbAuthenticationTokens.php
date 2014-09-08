<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/AuthenticationTokens.php');

class DbAuthenticationTokens extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\AuthenticationTokens');
        $this->dbToJson = array(
              'AuthenticationTokensID' => 'authenticationTokensID'
            , 'CustomersID'            => 'customersID'
            , 'RemoteUserID'           => 'remoteUserID'
            , 'TokenType'              => 'tokenType'
            , 'Token'                  => 'token'
            , 'CreatedAt'              => 'createdAt'
            , 'ExpiresAt'              => 'expiresAt'
            , 'Meta'                   => 'meta'
        );
        parent::secondaryInit();
    }
}