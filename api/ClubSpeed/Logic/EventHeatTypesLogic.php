<?php

namespace ClubSpeed\Logic;

class EventHeatTypesLogic extends BaseLogic {

    /**
     * Constructs a new instance of the EventHeatTypesLogic class.
     *
     * The EventHeatTypesLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->eventHeatTypes;
    }
}
