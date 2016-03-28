<?php

use ClubSpeed\Database\Helpers\UnitOfWork as UnitOfWork;

abstract class BaseCompositeApi extends BaseApi {

    public function get1($id1, $request_data) {
        throw new \RestException(404); // disallow get by 1 id
    }
    public function put1($id1, $request_data) {
        throw new \RestException(404); // disallow update by 1 id
    }
    public function delete1($id1) {
        throw new \RestException(404); // disallow delete by 1 id
    }

    /**
     * @url GET /:id1/:id2
     */
    public function get2($id1, $id2, $request_data) {
        try {
            $this->validate('get', $id1, $id2);
            $uow = UnitOfWork::build($request_data)
                ->action('get')
                ->table_id(array($id1, $id2));
            $mapper =& $this->mapper;
            $interface =& $this->interface;
            $interface->uow($uow);
            $data = $mapper->out($uow->data);
            return $data;
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url PUT /:id1/:id2
     */
    public function put2($id1, $id2, $request_data) {
        try {
            $this->validate('put', $id1, $id2);
            $uow = UnitOfWork::build($request_data)
                ->action('update')
                ->table_id(array($id1, $id2));
            $mapper =& $this->mapper;
            $interface =& $this->interface;
            $mapper->uowIn($uow);
            $interface->uow($uow);
            return; // empty body return
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url DELETE /:id1/:id2
     */
    public function delete2($id1, $id2, $request_data) {
        try {
            $this->validate('delete', $id1, $id2);
            $uow = UnitOfWork::build($request_data)
                ->action('delete')
                ->table_id(array($id1, $id2));
            $interface =& $this->interface;
            $interface->uow($uow);
            return; // empty body return
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }
}
