<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed categories.
 */
class HeatTypeProductsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the HeatTypeProductsLogic class.
     *
     * The HeatTypeProductsLogic constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the LogicContainer from which this class will been loaded.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->heattypeproducts;
    }
}
