<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed memberships.
 */
class MembershipsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the MembershipsLogic class.
     *
     * The MembershipsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->memberships;

        $this->before('uow', function($uow) {
            switch($uow->action) {
                case 'create':
                    if (empty($uow->data->ByUserID))
                        $uow->data->ByUserID = 1;
                    if (empty($uow->data->Notes))
                        $uow->data->Notes = '';
                    break;
            }
        });
    }
}