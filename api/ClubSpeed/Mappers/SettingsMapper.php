<?php

namespace ClubSpeed\Mappers;

class SettingsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'settings';
        $this->register(array(
              'SettingsID'      => ''
            , 'Namespace'       => ''
            , 'Name'            => ''
            , 'Type'            => ''
            , 'DefaultValue'    => 'default' // sql using DefaultValue, since default is a keyword
            , 'Value'           => ''
            , 'Description'     => ''
            , 'Created'         => ''
            , 'IsPublic'        => ''
        ));
    }
}