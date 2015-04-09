<?php

namespace ClubSpeed\Mappers;

class CustomerStatusMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'customerStatus';
        $this->register(array(
              'StatusID'                 => ''
            , 'Description'              => ''
            , 'Color'                    => ''
            , 'ShowOn1'                  => ''
            , 'ShowOn2'                  => ''
            , 'ShowOn3'                  => ''
            , 'ShowOn4'                  => ''
            , 'Deleted'                  => ''
        ));
    }
}