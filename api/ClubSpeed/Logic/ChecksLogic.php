<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed checks.
 */
class ChecksLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ChecksLogic class.
     *
     * The ChecksLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->checks;

        $this->insertable = array(
              'CustID'
            , 'CheckType'
            // , 'CheckStatus'
            , 'CheckName'
            , 'UserID'
            // , 'CheckTotal'
            , 'BrokerName'
            , 'Notes'
            , 'Gratuity'
            , 'Fee'
            // , 'OpenedDate'
            // , 'ClosedDate'
            , 'IsTaxExempt'
            , 'Discount'
            , 'DiscountID'
            , 'DiscountNotes'
            , 'DiscountUserID'
            , 'InvoiceDate'
        );

        $this->updatable = array(
              // 'CustID'
              'CheckType'
            , 'CheckStatus'
            , 'CheckName'
            , 'UserID'
            // , 'CheckTotal'
            , 'BrokerName'
            , 'Notes'
            , 'Gratuity'
            , 'Fee'
            // , 'OpenedDate'
            , 'ClosedDate'
            , 'IsTaxExempt'
            , 'Discount'
            , 'DiscountID'
            , 'DiscountNotes'
            , 'DiscountUserID'
            , 'InvoiceDate'
        );
    }

    // override and check for foreign keys, apply defaults
    public function create($params = array()) {
        $db =& $this->db;
        // note that in 5.4+, we can just reference $this inside the closure
        // and then $this can properly access private and protected items
        return parent::_create($params, function($check) use (&$db) {

            // validate physical structure before checking for foreign keys
            $check->validate('insert');

            // validate the customer "foreign key", as the database does not actually have a foreign key
            $customer = $db->customers->get($check->CustID);
            if (is_null($customer))
                throw new \RecordNotFoundException("Check create could not find customer in the database for the given customerId! Received: " . $check->CustID);
            
            // validate the user "foreign key", as the database does not actually have a foreign key
            $user = $db->users->get($check->UserID);
            if (is_null($user))
                throw new \RecordNotFoundException("Check create could not find user in the database for the given userId! Received: " . $check->UserID);
            
            $check->CheckStatus = 0; // check status should be overridden/defaulted to 0 (matches CheckStatus.OPEN from VB)
            $check->OpenedDate = \ClubSpeed\Utility\Convert::getDate();
            
            return $check; // use reference instead of return?
        });
    }

    public function applyCheckTotal($id) {
        if (!isset($id) || is_null($id))
            throw new \RequiredArgumentMissingException("Checks ApplyCheckTotal received an empty CheckID!");
        $sql = "EXEC dbo.ApplyCheckTotal :checkId";
        $params = array(":checkId" => $id);
        return $this->db->exec($sql, $params);
    }
}