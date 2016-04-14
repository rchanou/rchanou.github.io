<?php

namespace ClubSpeed\Documentation\API;

class DocBookingAvailability Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id       = 'booking-availability';
        $this->header   = 'Booking Availability';
        $this->url      = 'bookingAvailability';
        $this->json     = $this->json();
        $this->preface  = $this->preface();
        $this->info     = $this->info();
        $this->readonly = true;
    }

    private function preface() {
      return <<<EOS
<h4>Description</h4>
<p>
  <code class="prettyprint">BookingAvailability</code> is a helper, read-only resource designed
  to handle collecting the current availability for an online <code class="prettyprint">Booking</code>,
  which takes into account both online and local bookings and reservations.
</p>
EOS;
    }

    private function json() {
      return <<<EOS
{
  "bookings": [
    {
      "heatId": 4344,
      "heatDescription": "12 Minute Session",
      "heatStartsAt": "2014-10-08T20:20:00.00",
      "heatSpotsTotalActual": 22,
      "heatSpotsAvailableCombined": 21,
      "heatSpotsAvailableOnline": 21,
      "heatTypeId": 23,
      "isPublic": true,
      "products": [
        {
          "onlineBookingsId": 4,
          "price1": 35,
          "productDescription": "SK 10 Minutes",
          "productsId": 5,
          "productSpotsAvailableOnline": 5,
          "productSpotsTotal": 5,
          "productType": "PointItem"
        }
      ]
    }
  ]
}
EOS;
    }

    private function info() {
        return array(
            array(
                  'name'        => 'heatId'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the <a href="#heat-main">heat</a> for the <a href="#booking">booking</a>.'
            )
            , array(
                  'name'        => 'heatDescription'
                , 'type'        => 'String'
                , 'description' => 'The description of the <a href="#heat-main">heat</a>.'
            )
            , array(
                  'name'        => 'heatStartsAt'
                , 'type'        => 'DateTime'
                , 'description' => 'The start time for the <a href="#heat-main">heat</a>.'
            )
            , array(
                  'name'        => 'heatSpotsTotalActual'
                , 'type'        => 'Integer'
                , 'description' => 'The original total spots for the <a href="#heat-main">heat</a>.'
            )
            , array(
                  'name'        => 'heatSpotsAvailableCombined'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the <a href="#heat-main">heat</a> for the <a href="#booking">booking</a>.'
            )
            , array(
                  'name'        => 'heatSpotsAvailableOnline'
                , 'type'        => 'Integer'
                , 'description' => 'The number of spots intended to be exposed to the online interface for the entire <a href="#heat-main">heat</a>.'
            )
            , array(
                  'name'        => 'products'
                , 'type'        => 'Array<Products>'
                , 'description' => 'The container for Products objects.'
            )
            , array(
                  'name'        => 'products.onlineBookingsId'
                , 'type'        => 'Integer'
                , 'description' => 'The ID for the <a href="#booking">booking</a>.'
            )
            , array(
                  'name'        => 'products.price1'
                , 'type'        => 'Double'
                , 'description' => 'The price of the <a href="#products">product</a> for the <a href="#booking">booking</a>.'
            )
            , array(
                  'name'        => 'products.productDescription'
                , 'type'        => 'String'
                , 'description' => 'The description of the <a href="#products">product</a> for the <a href="#booking">booking</a>.'
            )
            , array(
                  'name'        => 'products.productsId'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the <a href="#products">product</a> for the <a href="#booking">booking</a>.'
            )
            , array(
                  'name'        => 'products.productSpotsAvailableOnline'
                , 'type'        => 'Integer'
                , 'description' => 'The number of spots intended to be exposed to the online interface for this specific <a href="#products">product</a>.'
            )
            , array(
                  'name'        => 'products.productSpotsTotal'
                , 'type'        => 'Integer'
                , 'description' => 'The total number of spots originally made available through this <a href="#products">product</a> pairing.'
            )
            , array(
                  'name'        => 'products.productType'
                , 'type'        => 'Integer'
                , 'description' => 'The ID of the type for the <a href="#products">product</a>.'
            )
        );
    }
}