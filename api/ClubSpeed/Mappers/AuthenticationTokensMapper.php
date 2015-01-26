<?php

namespace ClubSpeed\Mappers;

class AuthenticationTokensMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'authenticationTokens';
        $this->register(array(
              'AuthenticationTokensID' => ''
            , 'CustomersID'            => ''
            , 'RemoteUserID'           => ''
            , 'TokenType'              => ''
            , 'Token'                  => ''
            , 'CreatedAt'              => ''
            , 'ExpiresAt'              => ''
            , 'Meta'                   => ''
        ));
    }
}