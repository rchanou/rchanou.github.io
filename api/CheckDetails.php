<?php

use ClubSpeed\Database\Helpers\UnitOfWork as UnitOfWork;

class CheckDetails extends BaseUowApi {
    
    function __construct() {
        parent::__construct();
        $this->resource = 'CheckDetails';
    }

    /**
     * @url GET /
     */
    public function get($request_data = null) {
        try {
            $this->validate('all');
            $uow = UnitOfWork::build($request_data)->action('all');
            $uow->table($this->resource);
            $mapper = $this->mappers->{$this->resource};
            $interface = $this->logic->{$this->resource};
            $mapper->uowIn($uow);
            $interface->uow($uow);
            $data = $uow->data;
            $mapped = $mapper->out($data);
            return $mapped;
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url GET /:id
     */
    public function get1($id, $request_data = null) {
        try {
            $this->validate('get', $id);
            $uow = UnitOfWork::build($request_data)->action('get')->table_id($id);
            $uow->table($this->resource);
            $mapper = $this->mappers->{$this->resource};
            $interface = $this->logic->{$this->resource};
            $mapper->uowIn($uow);
            $interface->uow($uow);
            $data = $uow->data;
            $mapped = $mapper->out($data);
            return $mapped;
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }
}
