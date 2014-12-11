<?php

namespace ClubSpeed\Mappers;

class PointHistoryMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'pointHistory';
        $this->register(array(
              'PointHistoryID'     => ''
            , 'CustID'             => 'customerId'
            , 'CheckID'            => ''
            , 'UserID'             => ''
            , 'ReferenceID'        => ''
            , 'PointAmount'        => ''
            , 'Type'               => ''
            , 'PointDate'          => ''
            , 'PointExpDate'       => ''
            , 'Notes'              => ''
            , 'RefPointHistoryID'  => ''
            , 'IPAddress'          => 'ipAddress'
            , 'IsManual'           => ''
            , 'CheckDetailID'      => ''
            , 'ReservationID'      => ''
            , 'Username'           => ''
            , 'ApprovedByUserName' => ''
        ));
    }
}