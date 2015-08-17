<?php

namespace ClubSpeed\Logic;

use ClubSpeed\Utility\Convert;
use ClubSpeed\Utility\Types;

/**
 * The business logic class
 * for ClubSpeed locations.
 */
class LocationsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the LocationsLogic class.
     *
     * The LocationsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->locations;
        $self = $this; // silly php 5.3 scoping issues
        $this->before('uow', function($uow) use ($self) {
            switch($uow->action) {
                case 'create':
                    if (!empty($uow->data)) {
                        if (empty($uow->data->LocationID))
                            $uow->data->LocationID = $self->nextId();
                    }
                    break;
            }
        });
    }

    public function nextId() {
        $sql = ''
            ."\nSELECT ISNULL(MAX(l.LocationID) + 1, 1) AS LocationID"
            ."\nFROM dbo.Locations l"
            ;
        $result = $this->db->query($sql);
        $locationId = Convert::convert($result[0]['LocationID'], Types::$integer);
        return $locationId;
    }
}