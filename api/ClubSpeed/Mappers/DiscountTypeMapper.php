<?php

namespace ClubSpeed\Mappers;

class DiscountTypeMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->register(array(
              'DiscountID'     => ''
            , 'Description'    => ''
            , 'CalculateType'  => ''
            , 'Amount'         => ''
            , 'Enabled'        => ''
            , 'NeedApproved'   => ''
            , 'ProductClassID' => ''
            , 'Deleted'        => ''
        ));
    }
}
