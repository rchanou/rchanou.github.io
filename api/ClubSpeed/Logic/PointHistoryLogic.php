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
    }

    // override and check for foreign keys, apply defaults
    public function create($params = array()) {
        $db =& $this->db;
        return parent::_create($params, function($pointHistory) use (&$db) {
            if (!isset($pointHistory->CustID))
                throw new \RequiredArgumentMissingException("PointHistory create requires a CustID!");

            // require not null, non-zero PointAmount?
            if (isset($PointHistory->CheckID) && isset($PointHistory->CheckDetailID)) {
                // note that CheckID/CheckDetailID are not always required -- ie, when deducting points from a customer, 
                $existingPointHistory = $this->db->match(array(
                    "CheckID" => $pointHistory->CheckID,
                    "CheckDetailID" => $pointHistory->CheckDetailID
                ));
                if (!empty($existingPointHistory))
                    throw new \CSException("PointHistory create is attempting to add points from CheckDetails which have already been applied!");
            }
            
            return $pointHistory;
        });
    }
}