<?php

namespace ClubSpeed\Mappers;

class EventReservationTypesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventReservationTypes';
        $this->register(array(
              'ID' => 'eventReservationTypeId'
            , 'Description' => 'description'
            // , 'Picture' => 'picture'
        ));
    }
}
