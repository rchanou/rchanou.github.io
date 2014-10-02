<?php

use ClubSpeed\Enums\Enums as Enums;

class CheckTotals extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\CheckTotalsMapper();
        $this->interface        = $this->logic->checkTotals;
        $this->access['all']    = Enums::API_NO_ACCESS;
        $this->access['put']    = Enums::API_NO_ACCESS;
        $this->access['delete'] = Enums::API_NO_ACCESS;
    }

    public function post($id, $request_data = null) {
        $this->validate('post');
        try {
            if (isset($id)) {
                switch($id) {
                    case 'virtual':
                        $interface =& $this->interface;
                        return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
                            return $interface->virtual($mapped);
                        });
                }
            }
            return parent::post($id, $request_data);
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