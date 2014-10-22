<?php

class Queues
{
    public $restler;
    private $logic;

    function __construct(){
        header('Access-Control-Allow-Origin: *'); //Here for all /say
        $this->logic = isset($GLOBALS['logic']) ? $GLOBALS['logic'] : null;
    }

    public function post($request_data) {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            // there isn't a queues logic class, or underlying ORM definitions yet -- put logic here for now
            if (
                (isset($GLOBALS['cacheClearOverride']) && $GLOBALS['cacheClearOverride']) // global override is set and is truthy
                ||
                ($this->logic->version->compareToCurrent("15.4") > -1) // current club speed version is greater than or equal to 15.4
            ) {
                // we can use the queues logic!
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
                    // note that eventId of 0 also represents the base queue
                    $this->logic->customers->add_to_queue($customerId);
                }
                $GLOBALS['webapi']->clearCache();
            }
            else {
                // we don't have a ClubSpeed version high enough
                // to be manipulating queues through the API
                // due to missing WebAPI cache clearing remoting

                // NOTE(!!!): if we manipulate the queues without calling the WebAPI cache,
                // we put the front-end/intakes into a very strange state, where customers will
                // be visible missing from the queue, but cannot be added to the queue
                // through the front-end without first making a main-engine restart

                // just return -- a 200 success code here is fine
                // the tracks will need to manually add customers to the queues
                return;
            }
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