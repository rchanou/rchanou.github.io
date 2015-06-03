<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed discounts.
 */
class DiscountTypeLogic extends BaseLogic {

    /**
     * Constructs a new instance of the DiscountTypeLogic class.
     *
     * The DiscountTypeLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->discountType;
    }
}