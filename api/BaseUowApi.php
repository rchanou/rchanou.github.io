<?php

use ClubSpeed\Database\Helpers\UnitOfWork as UnitOfWork;

abstract class BaseUowApi extends BaseApi {

    /**
     * @url POST /
     */
    public function post($request_data = null) {
        try {
            $this->validate('create');
            $uow = UnitOfWork::build($request_data)->action('create');
            $this->_handle($uow);
            return $uow->table_id;
        }
        catch(Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url GET /
     *
     * Note: The @url stuff above isn't actually working for this.
     *       The function name actually has to be "get".
     *       Other methods could also have problems.
     */
    public function get($request_data = null) {
        try {
            $this->validate('all');
            $uow = UnitOfWork::build($request_data)->action('all');
            $this->_handle($uow);
            return $uow->data;
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url GET /count
     */
    public function getCount($request_data = null) {
        try {
            $this->validate('get'); // same access permissions as get (?)
            $uow = UnitOfWork::build($request_data)->action('count');
            $this->_handle($uow);
            return $uow->data;
        }
        catch (Exception $e) {
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
            $this->validate('get');
            $uow = UnitOfWork::build($request_data)->action('get')->table_id($id);
            $this->_handle($uow);
            return $uow->data;
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url PUT /:id
     *
     * Take over the BaseAPI functionality to test/prove out the UnitOfWork structure.
     */
    public function put1($id, $request_data = null) {
        try {
            $this->validate('put', $id); // pass the id along -- we need to be able to do this for customers to own their own data
            $uow = UnitOfWork::build($request_data)->action('update')->table_id($id);
            $uow = $this->_handle($uow);
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url DELETE /:id
     *
     * Delete a record with a single primary key.
     * Take over the BaseAPI functionality to test/prove out the UnitOfWork structure.
     */
    public function delete1($id, $request_data = null) {
        try {
            $this->validate('delete');
            $uow = UnitOfWork::build($request_data)->action('delete')->table_id($id);
            $uow = $this->_handle($uow);
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @access private
     *
     * Abstracted error handler.
     * Use to convert internal exceptions to RestExceptions as necessary.
     *
     * Note, @access doesn't actually work with Restler 3.0. 
     * Holding on to it for documentation purposes.
     * The function is named with an underscore to keep it out of the exposed API calls.
     */
    protected final function _error($e) {
        if ($e instanceof RestException)
            throw $e;
        if ($e instanceof CSException)
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        throw new RestException(500, $e->getMessage());
    }

    /**
     * @access private
     *
     * Boo. @access only works with Restler 3.0.
     */
    protected final function _handle(&$uow) {
        $uow->table($this->resource); // override in handle?
        $mapper = $this->mappers->{$this->resource};
        $interface = $this->logic->{$this->resource};
        $uow = $mapper->uow($uow, function($mapped) use (&$interface) {
            $interface->uow($mapped);
        });
        return $uow;
    }
}