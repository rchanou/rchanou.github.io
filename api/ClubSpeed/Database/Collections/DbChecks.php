<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/Checks.php');

class DbChecks extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\Checks');
        $this->dbToJson = array(
              'CheckID' => 'checkId'
            , 'CustID' => 'customerId'
            , 'CheckType' => 'type'
            , 'CheckStatus' => 'status'
            , 'CheckName' => 'name'
            , 'UserID' => 'userId'
            , 'CheckTotal' => 'total'
            , 'BrokerName' => 'broker'
            , 'Notes' => 'notes'
            , 'Gratuity' => 'gratuity'
            , 'Fee' => 'fee'
            , 'OpenedDate' => 'openedDate'
            , 'ClosedDate' => 'closedDate'
            , 'IsTaxExempt' => 'isExempt'
            , 'Discount' => 'discount'
            , 'DiscountID' => 'discountId'
            , 'DiscountNotes' => 'discountNotes'
            , 'DiscountUserID' => 'discountUserId'
            , 'InvoiceDate' => 'invoiceDate'
        );
        parent::secondaryInit();
    }

    // protected final function validate($type, $record) {
    //     switch (strtolower($type)) {
    //         case 'insert':
    //         if (is_null($record->UserID) || !is_int($record->UserID))
    //             throw new \RequiredArgumentMissingException("Check create requires UserID to be an integer! Received: " . $this->UserID);
    //         if (is_null($record->CustID) || !is_int($record->CustID))
    //             throw new \RequiredArgumentMissingException("Check create requires CustID to be an integer! Received: " . $this->CustID);
    //             break;
    //         case 'update':
    //             // todo
    //             break;
    //     }
    // }
}