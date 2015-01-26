<?php

use ClubSpeed\Enums\Enums as Enums;

class AuthenticationTokens extends BaseApi {
    
    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\AuthenticationTokensMapper();
        $this->interface = $this->logic->authenticationTokens;

        // $this->access['post'] = Enums::API_NO_ACCESS;


        // do we want to expose everything? anything at all? 
        // DEFINITELY all needs to be private at this point in time.
        // some restructure will be necessary if we ever implement true roles.

        // private keys are not yet implemented inside the authentication tokens table,
        // so we don't need to worry about private keys making more private keys.
    }
}