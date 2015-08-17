<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Database\Helpers\UnitOfWork;

/**
 * The business logic class
 * for ClubSpeed trigger logs.
 */
class TriggerLogsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the TriggerLogsLogic class.
     *
     * The TriggerLogsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->triggerLogs;
    }
}