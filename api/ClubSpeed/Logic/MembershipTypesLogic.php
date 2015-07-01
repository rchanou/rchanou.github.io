<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Database\Helpers\UnitOfWork;

/**
 * The business logic class
 * for ClubSpeed membership types.
 */
class MembershipTypesLogic extends BaseLogic {

    /**
     * Constructs a new instance of the MembershipTypesLogic class.
     *
     * The MembershipTypesLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->membershipTypes;

        // $this->on('uow', function($uow) { });
    }
}