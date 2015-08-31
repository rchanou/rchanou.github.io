<?php

use ClubSpeed\Database\Helpers\UnitOfWork;

class EventHeatDetails extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'EventHeatDetails';
    }

    public function get1($id1, $request_data = null) {
        throw new \RestException(404); // disallow get by 1 id
    }
    public function put1($id1, $request_data = null) {
        throw new \RestException(404); // disallow update by 1 id
    }
    public function delete1($id1, $request_data = null) {
        throw new \RestException(404); // disallow delete by 1 id
    }

    /**
     * @url GET /:id1/:id2
     */
    public function get2($id1, $id2, $request_data) {
        try {
            $this->validate('get');
            $uow = UnitOfWork::build($request_data)
                ->action('get')
                ->table_id(array($id1, $id2)); // note that order of ids does matter
            $this->_handle($uow);
            return $uow->data;
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
            $this->validate('put');
            $uow = UnitOfWork::build($request_data)
                ->action('update')
                ->table_id(array($id1, $id2));
            $uow = $this->_handle($uow);
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
            $this->validate('delete');
            $uow = UnitOfWork::build($request_data)
                ->action('delete')
                ->table_id(array($id1, $id2));
            $uow = $this->_handle($uow);
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }
}
