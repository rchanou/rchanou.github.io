<?php

namespace ClubSpeed\Mappers;

class GiftCardTransactionsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'transactions';
        $this->register(array(
              'CrdID'   => 'cardId'
            , 'Money'   => ''
            , 'Points'  => ''
            , 'Date'    => ''
            , 'Notes'   => ''
        ));
    }
}