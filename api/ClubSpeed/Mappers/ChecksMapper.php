<?php

namespace ClubSpeed\Mappers;

class ChecksMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'checks';
        $this->register(array(
              'CheckID'        => ''
            , 'CustID'         => 'customerId'
            , 'CheckType'      => 'type'
            , 'CheckStatus'    => 'status'
            , 'CheckName'      => 'name'
            , 'UserID'         => ''
            , 'CheckTotal'     => 'total'
            , 'BrokerName'     => 'broker'
            , 'Notes'          => ''
            , 'Gratuity'       => ''
            , 'Fee'            => ''
            , 'OpenedDate'     => ''
            , 'ClosedDate'     => ''
            , 'IsTaxExempt'    => ''
            , 'Discount'       => ''
            , 'DiscountID'     => ''
            , 'DiscountNotes'  => ''
            , 'DiscountUserID' => ''
            , 'InvoiceDate'    => ''
        ));
    }
}