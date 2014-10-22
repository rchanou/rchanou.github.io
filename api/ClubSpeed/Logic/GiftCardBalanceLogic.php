<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for gift card history sums.
 */
class GiftCardBalanceLogic extends BaseReadOnlyLogic {

    /**
     * Constructs a new instance of the GiftCardBalanceLogic class.
     *
     * The PaymentLogic constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the LogicContainer from which this class will been loaded.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->giftCardBalance_V;
    }
}