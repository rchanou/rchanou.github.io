<?php

namespace ClubSpeed\Mappers;

class ProductClassesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'productClasses';
        $this->register(array(
              'ProductClassID' => ''
            , 'Description'    => ''
            , 'Deleted'        => ''
            , 'ExportName'     => ''
        ));
    }
}
