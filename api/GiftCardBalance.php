<?php

use ClubSpeed\Enums\Enums as Enums;

class GiftCardBalance extends BaseUowApi {
    
    function __construct() {
        parent::__construct();
        $this->resource = 'giftCardBalance';
        $this->access['delete']     = Enums::API_NO_ACCESS;
        $this->access['post']       = Enums::API_NO_ACCESS;
        $this->access['put']        = Enums::API_NO_ACCESS;
        $this->access['register']   = Enums::API_PRIVATE_ACCESS;
    }

    /**
     * @url POST /register
     */
    function register($request_data) {
        $this->validate('register');
        try {
            // do any mapping here?
            $this->interface->register($request_data);
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