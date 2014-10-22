<?php

namespace ClubSpeed\Mappers;

class GiftCardBalanceMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'checks';
        $this->register(array(
              'CustID'          => 'customerId'
            , 'CrdID'           => 'cardId'
            , 'Balance'         => ''
        ));
    }
}