<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Strings as Strings;

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

    public function register($params = array()) {
        // stored proc flow:
        // check if customers exist by card ids
        // if customers don't exist
        //   * insert new customers
        //   * get the new custIds back
        // if customers do exist
        //   * get the cust ids back
        // if money is provided
        //   * zero out gift card history
        //   * increment gift card history
        // if points is provided
        //   * zero out point history
        //   * increment point history
        // if database is replicable
        //   * insert new customer records into trigger logs

        // todo: Accept 0s for zeroing out balance
        // consider null or non-existence to be a non-parameter
        $money      = @$params['money'];
        $points     = @$params['points'];
        $cards      = @$params['cards'];
        $userId     = @$params['userId'];
        $notes      = @$params['notes'];
        $name       = @$params['name'];
        $ipAddress  = @$_SERVER['REMOTE_ADDR'] ?: '';

        if (is_null($money) && is_null($points))
            throw new \CSException("Either points or money must be provided!");
        if (empty($cards))
            throw new \CSException("No cards were provided!");
        if (!is_null($money) && !is_numeric($money))
            throw new \CSException("Money received was non-numeric! Received: " . $money);
        if (!is_null($points) && !is_numeric($points))
            throw new \CSException("Points received was non-numeric! Received: " . $money);
        $cards = Strings::rangeToCSV($cards);
        $sql = "EXEC dbo.GiftCardRegistration ?, ?, ?, ?, ?, ?, ?";
        $params = array(
            $cards
            , $money
            , $points
            , $userId
            , $notes
            , $name
            , $ipAddress
        );
        $this->db->exec($sql, $params);
    }
}