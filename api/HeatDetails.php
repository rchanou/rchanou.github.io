<?php

class HeatDetails extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper = new \ClubSpeed\Mappers\HeatDetailsMapper();
        $this->interface = $this->logic->heatDetails;
    }

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
        $this->validate('get');
        return call_user_func_array(array($this, '_get'), func_get_args()); // need to pass on request_data in case it has "select"
    }

    /**
     * @url PUT /:id1/:id2
     */
    public function put2($id1, $id2, $request_data) {
        $this->validate('put');
        return call_user_func_array(array($this, '_put'), func_get_args());
    }

    /**
     * @url DELETE /:id1/:id2
     */
    public function delete2($id1, $id2, $request_data) {
        $this->validate('delete');
        return call_user_func_array(array($this, '_delete'), func_get_args());
    }
}