<?php

namespace ClubSpeed\Mappers;

class GiftCardHistoryMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'giftCards';
        $this->register(array(
              'HistoryID'          => 'giftCardHistoryId'
            , 'CustID'             => 'customerId'
            , 'UserID'             => ''
            , 'Points'             => ''
            , 'Type'               => ''
            , 'Notes'              => ''
            , 'CheckID'            => ''
            , 'CheckDetailID'      => ''
            , 'IPAddress'          => 'ipAddress'
            , 'TransactionDate'    => ''
            // , 'EurekasDBName'      => ''
            // , 'EurekasCheckID'     => ''
            // , 'EurekasPaidInvoice' => ''
        ));
    }
}