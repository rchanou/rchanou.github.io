<?php

namespace ClubSpeed\Logic;

class TasksForCheckEventLogic extends BaseLogic {

    /**
     * Constructs a new instance of the TasksForCheckEventLogic class.
     *
     * The TasksForCheckEventLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->tasksForCheckEvent;
        
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
