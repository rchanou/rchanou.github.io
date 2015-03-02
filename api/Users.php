<?php

class Users extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'users';
    }

    /**
     * @url POST /login
     * @url GET /login
     */
    public function login($request_data) {
        // do we want to validate permissions? public key? private key?
        // if (!\ClubSpeed\Security\Authenticate::privateAccess())
            // throw new RestException(403, "Invalid authorization!");
        try {
            $interface = $this->logic->{$this->resource};
            $mapper = $this->mappers->{$this->resource};
            $mapper->limit('client', array(
                  'userId'
                , 'username'
            ));
            $uow = $interface->login($request_data);
            if (empty($uow->data))
                throw new \UnauthorizedException();
            $mapper->uowOut($uow);
            return $uow->data;
        }
        catch(Exception $e) {
            $this->_error($e);
        }
    }
}