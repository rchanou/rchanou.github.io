<?php

namespace ClubSpeed\Mappers;

class MembershipsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'memberships';
        $this->register(array(
              'CustID'           => 'customerId'
            , 'MembershipTypeID' => 'membershipTypeId'
            , 'LastChanged'      => 'changed'
            , 'ExpirationDate'   => 'expiration'
            , 'Notes'            => ''
            , 'ByUserID'         => ''
        ));
    }
}