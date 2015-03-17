<?php

use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Database\Helpers\UnitOfWork;

class Customers extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'Customers';

        // allow customers to get and edit their own information
        $this->access['get'] = Enums::API_CUSTOMER_ACCESS;
        $this->access['put'] = Enums::API_CUSTOMER_ACCESS;
    }

    /**
     * @url GET /:id
     *
     * Take over the BaseAPI functionality to test/prove out the UnitOfWork structure.
     */
    public function get1($id, $request_data = null) {
        try {
            $this->validate('get', $id);
            $uow = UnitOfWork::build($request_data)->action('get')->table_id($id);
            $this->_handle($uow);
            return array(
                'customers' => array($uow->data) // hack to be backwards compatible with the iPhone app
            );
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url POST /login
     */
    public function login($request_data) {
        // note that racers->login is a copy of this method (not a pointer. we could make it one, if necessary.)
        // leave access wide open, since customers won't have a key until after this point
        try {
            $username   = @$request_data['username'];
            $password   = @$request_data['password'];
            $token      = @$request_data['token'];
            if (!empty($token))
                return $this->logic->customers->authenticate($token);
            else
                return $this->logic->customers->login($username, $password);
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
}