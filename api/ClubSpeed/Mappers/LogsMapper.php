<?php

namespace ClubSpeed\Mappers;

class LogsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'logs';
        $this->register(array(
              'LogID'        => 'logsId'
            , 'Message'      => ''
            , 'LogDate'      => 'date'
            , 'TerminalName' => 'terminal'
            , 'UserName'     => 'username'
        ));
    }
}