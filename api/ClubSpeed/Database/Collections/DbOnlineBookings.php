<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/OnlineBookings.php');

class DbOnlineBookings extends DbCollection {

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\OnlineBookings');
        $this->dbToJson = array(
            'OnlineBookingsID'  => 'onlineBookingsId'
            , 'HeatMainID'      => 'heatId'
            , 'IsPublic'        => 'isPublic'
            , 'ProductsID'      => 'productsId'
            , 'QuantityTotal'   => 'quantityTotal'
        );
        parent::secondaryInit();
    }
}