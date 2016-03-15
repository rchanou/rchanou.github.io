<?php

namespace ClubSpeed\Mappers;

class EventReservationLinkMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventReservationLinks';
        $this->register(array(
              'ID' => 'eventReservationLinkId'
            , 'CheckID' => 'checkId'
            , 'ReservationID' => 'reservationId'
        ));
    }
}
