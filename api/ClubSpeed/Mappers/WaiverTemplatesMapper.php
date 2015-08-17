<?php

namespace ClubSpeed\Mappers;

class WaiverTemplatesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'waivers';
        $this->register(array(
              'Waiver'      => 'waiverId'
            , 'Description' => ''
            , 'WaiverText'  => 'text'
        ));
    }
}