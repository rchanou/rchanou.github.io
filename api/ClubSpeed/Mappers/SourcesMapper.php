<?php

namespace ClubSpeed\Mappers;

class SourcesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'sources';
        $this->register(array(
              'SourceID'   => ''
            , 'SourceName' => 'name'
            , 'Enabled'    => ''
            , 'Seq'        => ''
            , 'Deleted'    => ''
            , 'CaboOnly'   => ''
            , 'Languages'  => ''
            , 'LocationID' => ''
        ));
    }
}