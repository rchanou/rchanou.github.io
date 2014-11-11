<?php

namespace ClubSpeed\Mappers;
use ClubSpeed\Utility\Arrays;

class ControlPanelMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'controlPanel';
        $this->register(array(
              'TerminalName'   => 'namespace'
            , 'SettingName'    => 'name'
            , 'DataType'       => 'type'
            , 'DefaultSetting' => 'default'
            , 'SettingValue'   => 'value'
            , 'Description'    => ''
            , 'Fixed'          => ''
            , 'CreatedDate'    => 'created'
        ));
    }
}