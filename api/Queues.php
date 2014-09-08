<?php

class Queues
{
    public $restler;
    private $logic;

    function __construct(){
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = isset($GLOBALS['logic']) ? $GLOBALS['logic'] : null;
    }

    // queues/add.json POST
    public function post($request_data) {
        if (!\ClubSpeed\Security\Validate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // determine which version of queues we have here
            $eventId    = (int)@$request_data['eventId'];
            $customerId = (int)@$request_data['customerId'];
            $checkId    = (int)@$request_data['checkId'];
            if (isset($eventId) && $eventId > 0) {
                // we have an event queue
                $this->logic->events->add_to_queue(
                      $eventId
                    , $customerId
                    , $checkId
                );
            }
            else {
                // assume we have a base customer queue
                $this->logic->customers->add_to_queue($customerId);
            }
            $GLOBALS['webapi']->clearCache();
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException(412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException($e->getCode() ?: 500, $e->getMessage());
        }
    }
}