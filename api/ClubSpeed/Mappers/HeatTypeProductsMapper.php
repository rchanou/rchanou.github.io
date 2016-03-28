<?php

namespace ClubSpeed\Mappers;

class HeatTypeProductsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'heattypeproducts';
        $this->register(array(
              'HeatTypeProductId'  => ''
            , 'HeatTypeNo'         => 'heatTypeId'
            , 'ProductID'          => ''
            , 'CreatedDate'        => ''
        ));
    }
}
