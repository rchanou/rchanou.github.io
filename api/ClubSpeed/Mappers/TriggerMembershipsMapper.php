<?php

namespace ClubSpeed\Mappers;

class TriggerMembershipsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'triggerMemberships';
        $this->register(array(
              'ID'               => 'triggerMembershipsId'
            , 'CustID'           => 'customerId'
            , 'MembershipTypeID' => ''
            , 'LastUpdated'      => ''
            , 'IsDeleted'        => ''
            , 'Deleted'          => ''
        ));
    }
}