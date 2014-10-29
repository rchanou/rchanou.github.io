<?php

namespace ClubSpeed\Documentation\API;

class DocBookingAvailability Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'booking-availability';
        $this->header          = 'Booking Availability';
        $this->url             = 'bookingAvailability';
        $this->info            = $this->info();
        $this->calls['list']   = $this->all(); // list is a reserved keyword in php
        $this->calls['match']  = $this->match();
        $this->calls['search'] = $this->search();
        $this->expand(); // expand before delete is added
    }

    private function info() {
        return array(
            array(
                  'name'        => 'heatId'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the heat for the booking.'
            )
            , array(
                  'name'        => 'heatDescription'
                , 'type'        => 'String'
                , 'description' => 'The description of the heat.'
            )
            , array(
                  'name'        => 'heatStartsAt'
                , 'type'        => 'DateTime'
                , 'description' => 'The start time for the heat.'
            )
            , array(
                  'name'        => 'heatSpotsTotalActual'
                , 'type'        => 'Integer'
                , 'description' => 'The original total spots for the heat.'
            )
            , array(
                  'name'        => 'heatSpotsAvailableCombined'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the heat for the booking.'
            )
            , array(
                  'name'        => 'heatSpotsAvailableOnline'
                , 'type'        => 'Integer'
                , 'description' => 'The number of spots intended to be exposed to the online interface for the entire heat.'
            )
            , array(
                  'name'        => 'products'
                , 'type'        => 'Array<Products>'
                , 'description' => 'The container for Products objects.'
            )
            , array(
                  'name'        => 'products.onlineBookingsId'
                , 'type'        => 'Integer'
                , 'description' => 'The ID for the booking.'
            )
            , array(
                  'name'        => 'products.price1'
                , 'type'        => 'Double'
                , 'description' => 'The price of the product for the booking.'
            )
            , array(
                  'name'        => 'products.productDescription'
                , 'type'        => 'String'
                , 'description' => 'The description of the product for the booking.'
            )
            , array(
                  'name'        => 'products.productsId'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the product for the booking.'
            )
            , array(
                  'name'        => 'products.productSpotsAvailableOnline'
                , 'type'        => 'Integer'
                , 'description' => 'The number of spots intended to be exposed to the online interface for this specific product.'
            )
            , array(
                  'name'        => 'products.productSpotsTotal'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the heat for the booking.'
            )
            , array(
                  'name'        => 'products.productType'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the type for the product.'
            )
        );
    }

    private function all() {
        return array(
            'info' => array(
                'access' => 'private'
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/bookingAvailability HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "heatId": 3,
      "heatDescription": "Event 10 min Qualifer",
      "heatStartsAt": "2013-11-25 23:30:00.000",
      "heatSpotsTotalActual": 10,
      "heatSpotsAvailableCombined": 3,
      "heatSpotsAvailableOnline": 5,
      "heatTypeId": 6,
      "products": [
        {
          "onlineBookingsId": 2,
          "price1": 5,
          "productDescription": "Cadet Member",
          "productsId": 11,
          "productSpotsAvailableOnline": 3,
          "productSpotsTotal": 5,
          "productType": "MembershipItem"
        }
      ]
    },
    {
      "heatId": 5,
      "heatDescription": "Event 10 min Qualifer",
      "heatStartsAt": "2013-11-25 22:45:00.000",
      "heatSpotsTotalActual": 10,
      "heatSpotsAvailableCombined": 4,
      "heatSpotsAvailableOnline": 3,
      "heatTypeId": 6,
      "products": [
        {
          "onlineBookingsId": 3,
          "price1": 10,
          "productDescription": "10 Minute AnD",
          "productsId": 2,
          "productSpotsAvailableOnline": 3,
          "productSpotsTotal": 3,
          "productType": "PointItem"
        }
      ]
    },
    {
      "heatId": 2,
      "heatDescription": "AnD 10 Min",
      "heatStartsAt": "2013-11-26 00:15:00.000",
      "heatSpotsTotalActual": 16,
      "heatSpotsAvailableCombined": 11,
      "heatSpotsAvailableOnline": 5,
      "heatTypeId": 1,
      "products": [
        {
          "onlineBookingsId": 4,
          "price1": 15,
          "productDescription": "Adult Member",
          "productsId": 8,
          "productSpotsAvailableOnline": 5,
          "productSpotsTotal": 5,
          "productType": "MembershipItem"
        }
      ]
    }
  ]
}
EOS
            )
        );
    }

    private function match() {
        return array(
            'info' => array(
                'access' => 'private'
            ),
            'examples' => array(
                'request' => <<<EOS
GET http://{$_SERVER['SERVER_NAME']}/api/index.php/bookingAvailability?heatId=3 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "heatId": 3,
      "heatDescription": "Event 10 min Qualifer",
      "heatStartsAt": "2013-11-25 23:30:00.000",
      "heatSpotsTotalActual": 10,
      "heatSpotsAvailableCombined": 3,
      "heatSpotsAvailableOnline": 5,
      "heatTypeId": 6,
      "products": [
        {
          "onlineBookingsId": 2,
          "price1": 5,
          "productDescription": "Cadet Member",
          "productsId": 11,
          "productSpotsAvailableOnline": 3,
          "productSpotsTotal": 5,
          "productType": "MembershipItem"
        }
      ]
    }
  ]
}
EOS
            )
        );
    }

    private function search() {
        return array(
            'info' => array(
                'access' => 'private'
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/bookingAvailability?filter=productSpotsAvailableOnline \$gte 5 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "heatId": 2,
      "heatDescription": "AnD 10 Min",
      "heatStartsAt": "2013-11-26 00:15:00.000",
      "heatSpotsTotalActual": 16,
      "heatSpotsAvailableCombined": 11,
      "heatSpotsAvailableOnline": 5,
      "heatTypeId": 1,
      "products": [
        {
          "onlineBookingsId": 4,
          "price1": 15,
          "productDescription": "Adult Member",
          "productsId": 8,
          "productSpotsAvailableOnline": 5,
          "productSpotsTotal": 5,
          "productType": "MembershipItem"
        }
      ]
    }
  ]
}
EOS
            )
        );
    }
}