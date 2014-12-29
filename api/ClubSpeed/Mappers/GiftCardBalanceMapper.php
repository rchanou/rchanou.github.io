<?php

namespace ClubSpeed\Mappers;

class GiftCardBalanceMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'cards';
        $this->register(array(
              'CrdID'           => 'cardId'
            , 'IsGiftCard'      => ''
            , 'Points'          => ''
            , 'Money'           => ''
        ));
    }
}