<?php

namespace ClubSpeed\Mappers;

class TriggerLogsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'triggerLogs';
        $this->register(array(
              'ID'          => 'triggerLogsId'
            , 'CustID'      => 'customerId'
            , 'LastUpdated' => ''
            , 'TableName'   => 'table'
            , 'Type'        => ''
            , 'Deleted'     => ''
        ));
    }
}