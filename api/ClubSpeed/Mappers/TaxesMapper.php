<?php

namespace ClubSpeed\Mappers;

class TaxesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'taxes';
        $this->register(array(
              'TaxID'         => ''
            , 'Description'   => ''
            , 'Amount'        => ''
            , 'Deleted'       => ''
            , 'GST'           => 'gst'
        ));
    }
}