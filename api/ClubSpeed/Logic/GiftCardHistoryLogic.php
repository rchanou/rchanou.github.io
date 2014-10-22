<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed logs.
 */
class GiftCardHistoryLogic extends BaseLogic {

    /**
     * Constructs a new instance of the LogsLogic class.
     *
     * The LogsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->giftCardHistory;
    }

    public function create($params = array()) {
        $interface =& $this->interface;
        return $this->_create($params, function($giftCardHistory) use (&$interface) {
            $giftCardHistory->TransactionDate = \ClubSpeed\Utility\Convert::getDate();
            if (!isset($giftCardHistory->Type))
                $giftCardHistory->Type = 0; // or should this be enforced? equivalent to GiftCardHistoryType.SellGiftCard
            if (!isset($giftCardHistory->UserID))
                $giftCardHistory->UserID = 0;
            return $giftCardHistory;
        });
    }
}