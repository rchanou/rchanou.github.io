<?php

namespace ClubSpeed\Mappers;

class EventStatusMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventStatuses';
        $this->register(array(
              'ID' => 'eventStatusId'
            , 'ColorID' => 'colorId'
            , 'Seq' => 'seq'
            , 'Status' => 'status'
        ));
    }
}
