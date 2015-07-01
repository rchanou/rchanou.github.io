<?php

namespace ClubSpeed\Mappers;

class MembershipTypesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'membershipTypes';
        $this->register(array(
              'MembershipTypeID' => ''
            , 'Description'      => ''
            , 'IsExpires'        => 'expires'
            , 'Seq'              => ''
            , 'Enabled'          => ''
            , 'ExpirationType'   => ''
            , 'PriceLevel'       => ''
        ));
    }
}