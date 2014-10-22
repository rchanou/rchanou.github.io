<?php

use ClubSpeed\Enums\Enums as Enums;

class CheckTotals extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper            = new \ClubSpeed\Mappers\CheckTotalsMapper();
        $this->interface         = $this->logic->checkTotals;
        $this->access['all']     = Enums::API_NO_ACCESS;
        $this->access['put']     = Enums::API_NO_ACCESS;
        $this->access['delete']  = Enums::API_NO_ACCESS;
        $this->access['virtual'] = Enums::API_PRIVATE_ACCESS;
    }

    /**
     * @url POST /virtual
     */
    public function virtual($request_data = null) {
        $this->validate('virtual');
        try {
            $data = $this->mapper->in($request_data);
            $mapped = $data->params;
            $giftCards = @$request_data['giftCards'] ?: array();
            $virtual = $this->interface->virtual($mapped, $giftCards);
            $out = $this->mapper->out($virtual);
            return $out;
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