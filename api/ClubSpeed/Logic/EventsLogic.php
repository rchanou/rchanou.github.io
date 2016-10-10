<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed events.
 */
class EventsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the EventsLogic class.
     *
     * The EventsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->events;

        $self =& $this;

        $afters = array(
            'create' => array($self, 'clearCache'),
            'update' => array($self, 'clearCache'),
            'delete' => array($self, 'clearCache')
        );
        $this->after('uow', function($uow) use (&$afters) {
            if (isset($afters[$uow->action]))
                call_user_func($afters[$uow->action], $uow);
        });
    }

    function clearCache($uow) {
        $GLOBALS['webapi']->clearCache();
    }
}
