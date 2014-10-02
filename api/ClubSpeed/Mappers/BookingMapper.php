<?php

namespace ClubSpeed\Mappers;

class BookingMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'bookings';
        $this->register(array(
            'OnlineBookingsID'  => ''
            , 'HeatMainID'      => 'heatId'
            , 'IsPublic'        => ''
            , 'ProductsID'      => ''
            , 'QuantityTotal'   => ''
        ));
    }
}