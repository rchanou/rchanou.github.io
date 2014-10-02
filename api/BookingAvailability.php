<?php

use ClubSpeed\Enums\Enums as Enums;

class BookingAvailability extends BaseApi {
    
    function __construct() {
        parent::__construct();
        $this->mapper               = new \ClubSpeed\Mappers\BookingAvailabilityMapper();
        $this->interface            = $this->logic->bookingAvailability;
        $this->access['post']       = Enums::API_NO_ACCESS;
        $this->access['put']        = Enums::API_NO_ACCESS;
        $this->access['delete']     = Enums::API_NO_ACCESS;
        $this->access['range']      = Enums::API_PRIVATE_ACCESS;
        $this->access['visible']    = Enums::API_PRIVATE_ACCESS;
    }

    /**
     * @url GET /range
     * Note that the above is more than just a phpdoc -- it also handles routing
     */
    public function range($request_data = null) {
        $this->validate('range');
        try {
            $data = $this->interface->range($request_data);
            // can't use mutate with the callback structure,
            //  as the 'start' and 'end' parameters will not be valid items in the JSON map
            $mapped = $this->mapper->mutate($data); 
            return $mapped;
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

    /**
     * @url GET /visible
     * Note that the above is more than just a phpdoc -- it also handles routing
     */
    public function visible($request_data = null) {
        $this->validate('visible');
        try {
            $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
                return $interface->visible();
            });
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