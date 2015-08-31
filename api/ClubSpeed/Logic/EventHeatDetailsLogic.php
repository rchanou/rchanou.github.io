<?php

namespace ClubSpeed\Logic;

use ClubSpeed\Utility\Convert;

/**
 * The business logic class
 * for ClubSpeed event heat details.
 */
class EventHeatDetailsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the EventHeatDetailsLogic class.
     *
     * The EventHeatDetailsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->eventHeatDetails;

        $self =& $this;

        $befores = array(
            'create' => array($self, 'beforeCreate') // awful php style "function pointers"
        );
        $this->before('uow', function($uow) use (&$befores)  {
            if (isset($befores[$uow->action]))
                call_user_func($befores[$uow->action], $uow);
        });

        $afters = array(
            'create' => array($self, 'afterCreate'),
            'update' => array($self, 'afterUpdate')
        );
        $this->after('uow', function($uow) use (&$afters) {
            if (isset($afters[$uow->action]))
                call_user_func($afters[$uow->action], $uow);
        });
    }

    function beforeCreate($uow) {
        // no nullable fields
        $data =& $uow->data;
        if (!$this->logic->events->exists($data->EventID))
            throw new \RecordNotFoundException('Events', $data->EventID);
        if (!$this->logic->customers->exists($data->CustID))
            throw new \RecordNotFoundException('Customers', $data->CustID);
        if (!isset($data->RPM))
            $data->RPM = 1200; // match customer rpm default
        if (!isset($data->DateAdded))
            $data->DateAdded = Convert::getDate();
        if (!isset($data->CheckID))
            $data->CheckID = 0; // match db default
        if (!isset($data->RoundLoseNum))
            $data->RoundLoseNum = 99; // match vb default (?)
        if (!isset($data->TotalRacesInEvent))
            $data->TotalRacesInEvent = 0; // match db default
        return $uow;
    }

    function afterCreate($uow) {
        $GLOBALS['webapi']->clearCache();
    }

    function afterUpdate($uow) {
        $GLOBALS['webapi']->clearCache();
    }
}
