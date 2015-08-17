<?php

namespace ClubSpeed\Mappers;

class LocationsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'locations';
        $this->register(array(
              'LocationID'      => 'locationsId'
            , 'LocationName'    => 'name'
            , 'IPAddress'       => 'ipAddress'
            , 'TimeoutMS'       => 'timeoutInMs'
            , 'IsCentralServer' => ''
        ));
    }
}