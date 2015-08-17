<?php

namespace ClubSpeed\Mappers;

class EventContactStatusLookupMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventContactStatusLookup';
        $this->register(array(
              'id'          => 'eventContactStatusLookupId'
            , 'value'       => ''
            , 'description' => ''
            , 'typeId'      => ''
            , 'orderby'     => 'orderBy'
            , 'Deleted'     => ''
            , 'Locked'      => ''
        ));
    }
}