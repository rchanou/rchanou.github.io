<?php

use ClubSpeed\Enums\Enums as Enums;

class Payments extends BaseUowApi {

    function __construct() {
        parent::__construct();
        $this->resource = 'Payment';
    }

    /**
     * @url POST /:id/void
     */
    public function void($id, $request_data = null) {
        $this->validate('void');
        try {
            $interface = $this->logic->{$this->resource}->void($id);
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