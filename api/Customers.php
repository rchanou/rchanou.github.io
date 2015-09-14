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
     * @url GET /primary
     */
    public function primary($request_data) {
        try {
            $this->validate('all');
            $uow = UnitOfWork::build($request_data);
            $mapper = $this->mappers->{$this->resource};
            $mapper->uowIn($uow);
            $where = $uow->where;
            if (empty($where))
                throw new CSException('Attempted to find primary customer without any where clause!');
            $primary = $this->logic->{$this->resource}->primary($where);
            if (empty($primary))
                throw new CSException('Unable to find primary customer matching the given criteria: (' . json_encode($where) . ')', 404);
            $uow->data = $this->logic->{$this->resource}->primary($where);
            $mapper->uowOut($uow);
            return $uow->data;
        }
        catch(Exception $e) {
            $this->_error($e);
        }
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
        catch (Exception $e) {
            $this->_error($e);
        }
    }
}
