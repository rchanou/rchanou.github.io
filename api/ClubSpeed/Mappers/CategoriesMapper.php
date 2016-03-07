<?php

namespace ClubSpeed\Mappers;

class CategoriesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'categories';
        $this->register(array(
              'CategoryID'  => ''
            , 'Description' => ''
            , 'Enabled'     => ''
            , 'SEQ'         => 'seq'
            // , 'largeIcon'   => '' // potentially a lot of unnecessary data. might be worth removing from the definition.
            , 'Deleted'     => ''
            , 'IsCombo'     => ''
        ));
    }
}
