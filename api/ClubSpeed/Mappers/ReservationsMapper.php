<?php

namespace ClubSpeed\Mappers;

class ReservationsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'reservations';
        $this->register(array(
              'OnlineBookingReservationsID'      => 'onlineBookingReservationsId'
            , 'OnlineBookingsID'                 => 'onlineBookingsId'
            , 'CustomersID'                      => 'customersId'
            , 'SessionID'                        => 'sessionId'
            , 'Quantity'                         => 'quantity'
            , 'CreatedAt'                        => 'createdAt'
            , 'ExpiresAt'                        => 'expiresAt'
            , 'OnlineBookingReservationStatusID' => 'onlineBookingReservationStatusId'
            , 'CheckID'                          => 'checkId'
        ));
    }
}
