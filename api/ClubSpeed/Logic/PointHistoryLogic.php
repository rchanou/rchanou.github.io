<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed checks.
 */
class PointHistoryLogic extends BaseLogic {

    /**
     * Constructs a new instance of the PointHistoryLogic class.
     *
     * The PointHistoryLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->pointHistory;

        $this->before('uow', function($uow) use ($db) {
            switch($uow->action) {
                case 'create':
                    $pointHistory =& $uow->data;
                    if (empty($pointHistory) || empty($pointHistory->CustID))
                        throw new \RequiredArgumentMissingException("PointHistory create requires a CustID!");
                    if (isset($pointHistory->CheckID) && isset($pointHistory->CheckDetailID)) {
                        // note that CheckID/CheckDetailID are not always required -- ie, when deducting points from a customer, 
                        $existingPointHistory = $this->db->match(array(
                            "CheckID" => $pointHistory->CheckID,
                            "CheckDetailID" => $pointHistory->CheckDetailID
                        ));
                        if (!empty($existingPointHistory))
                            throw new \CSException("PointHistory create is attempting to add points from CheckDetails which have already been applied!");
                    }
                    break;
            }
        });
    }
}