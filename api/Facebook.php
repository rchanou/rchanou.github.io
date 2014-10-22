<?php

use ClubSpeed\Enums\Enums as Enums;

class Facebook extends BaseApi {

    function __construct() {
        parent::__construct();
        $this->mapper           = new \ClubSpeed\Mappers\FacebookMapper();
        $this->interface        = $this->logic->facebook;
        $this->access['login']  = Enums::API_NO_ACCESS; // prevent access for now - will throw a 404
    }

    /**
     * @url POST /login
     */
    public function login($request_data) {
        // we can use this, if we deem it necessary
        // seems like a more normal spot for facebook login to exist
        $this->validate('login');
        try {
            // TODO (!!!)
            // $interface =& $this->interface; // PHP 5.3 hack for callbacks and $this
            // return $this->mapper->mutate($request_data, function($mapped) use (&$interface) {
            //     return $interface->login($mapped);
            // });
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