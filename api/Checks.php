<?php

use ClubSpeed\Enums\Enums as Enums;

class Checks extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper                       = new \ClubSpeed\Mappers\ChecksMapper();
        $this->interface                    = $this->logic->checks;
        $this->access['delete']             = Enums::API_NO_ACCESS;
        $this->access['applyCheckTotal']    = Enums::API_PRIVATE_ACCESS;
    }

    /**
     * @url POST /:id/void
     */
    public function void($id, $request_data = null) {
        $this->validate('void');
        try {
            $this->interface->void($id); // expose this, or call by default?
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url POST /:id/finalize
     */
    public function finalize($id, $request_data = array()) {
        $this->validate('finalize');
        try {
            return $this->interface->finalize($id, $request_data);
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }

    /**
     * @url POST /:id/receipt
     */
    public function receipt($id, $request_data = array()) {
        $this->validate('receipt');
        try {
            return $this->interface->receipt($id);
        }
        catch (Exception $e) {
            $this->_error($e);
        }
    }
}