<?php

namespace ClubSpeed\Documentation\API;

class DocBooking Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'booking';
        $this->header  = 'Booking';
        $this->url     = 'booking';
        $this->json    = $this->json();
        $this->preface = $this->preface();
        $this->info    = $this->info();
    }

        private function preface() {
      return <<<EOS
<h4>Description</h4>
<p>
  A <code class="prettyprint">Booking</code> is a record designed to expose a <code class="prettyprint">Heat</code> to the Online Booking interface.
  To make a booking available, create a record with a <code class="prettyprint">HeatMain.heatId</code>
  and <code class="prettyprint">Product.productsId</code> pairing,
  which will indicate that a place in the <code class="prettyprint">Heat</code>
  can be purchased by buying one of the connected <code class="prettyprint">Product</code>.
</p>
EOS;
    }

    private function json() {
      return <<<EOS
{
  "bookings": [
    {
      "onlineBookingsId": 4,
      "heatId": 4344,
      "productsId": 5,
      "isPublic": true,
      "quantityTotal": 5
    }
  ]
}
EOS;
    }

    private function info() {
        return array(
            array(
                  'name'        => 'onlineBookingsId'
                , 'type'        => 'Integer'
                , 'default'     => '{Generated}'
                , 'required'    => true
                , 'description' => 'The primary key for the record'
            )
            , array(
                  'name'        => 'heatId'
                , 'type'        => 'Integer'
                , 'required'    => true
                , 'description' => 'The ID of the <a href="#heat-main">heat</a> for the booking'
            )
            , array(
                  'name'        => 'productsId'
                , 'type'        => 'Integer'
                , 'required'    => true
                , 'description' => 'The ID of the <a href="#products">product</a> for the booking'
            )
            , array(
                  'name'        => 'isPublic'
                , 'type'        => 'Boolean'
                , 'default'     => "true"
                , 'description' => "The flag indicating whether or not to make this booking available to the online booking interface"
            )
            , array(
                  'name'        => 'quantityTotal'
                , 'type'        => 'Integer'
                , 'required'    => true
                , 'description' => "The total number of booking reservations to make available. This must be a positive integer"
            )
        );
    }
}