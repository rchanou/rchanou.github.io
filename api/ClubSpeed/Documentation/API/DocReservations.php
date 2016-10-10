<?php

namespace ClubSpeed\Documentation\API;

class DocReservations Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'reservations';
        $this->header  = 'Reservations';
        $this->url     = 'reservations';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
      return <<<EOS
<h4>Description</h4>
<p>
  A <code class="prettyprint">Reservation</code> is an indication that a <code class="prettyprint">Customer</code> is either
  looking to purchase a spot in a Booking, or has already purchased a spot in a <code class="prettyprint">Booking</code>.
  <code class="prettyprint">Reservations</code>
  should be used by creating a new temporary <code class="prettyprint">Reservation</code>
  whenever a <code class="prettyprint">Customer</code> adds a <code class="prettyprint">Booking</code> to their kart,
  and then updated to be made permanent whenever the purchase has been successfully made.
</p>
<p>
  Please note that a <code class="prettyprint">Reservation</code> does <span style="font-style:italic">not</span>
  correspond directly to <code class="prettyprint">HeatMain.numberOfReservation</code>. A <code class="prettyprint">Reservation</code>
  corresponds directly to a <code class="prettyprint">Booking</code> and represents either a permanent or temporary hold
  on a <code class="prettyprint">Booking</code>.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "reservations": [
    {
      "onlineBookingReservationsId": 428,
      "onlineBookingsId": 514,
      "customersId": 1232722,
      "sessionId": "3e123f90551bf2feaadce4ea53482f4fa23f6cc5",
      "quantity": 1,
      "createdAt": "2015-12-09T16:19:05.17",
      "expiresAt": "2016-12-09T17:19:05.17",
      "onlineBookingReservationStatusId": 1,
      "checkId": 17349
    }
  ]
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "onlineBookingReservationId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "checkId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the <a href=\"#checks\">check</a> for the reservation, where applicable. If a relevant checkId is available, it should be set, as this enables auto-voiding abandoned checks with offsite payment processors."
            ),
            array(
                "name" => "createdAt",
                "type" => "DateTime",
                "default" => "{Now}",
                "required" => false,
                "description" => "The timestamp at which the reservation was created"
            ),
            array(
                "name" => "customersId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The ID of the <a href=\"#customers\">customer</a> for the reservation"
            ),
            array(
                "name" => "expiresAt",
                "type" => "DateTime",
                "default" => "{Now + default length from control panel}",
                "required" => false,
                "description" => "The timestamp at which the reservation will be automatically expired, if status is not set to Permanent"
            ),
            array(
                "name" => "onlineBookingReservationStatusId",
                "type" => "Integer",
                "default" => "1",
                "required" => false,
                'description' => ''
                  ."\n<p>"
                  ."\n  The ID for the status of the reservation. Statuses should be set to Temporary while the underlying kart is open, and set to Permanent after the purchase has been made"
                  ."\n</p>"
                  ."\n<ol>"
                  ."\n  <li>Temporary</li>"
                  ."\n  <li>Permanent</li>"
                  ."\n</ol>"
            ),
            array(
                "name" => "onlineBookingsId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The ID of the <a href=\"#bookings\">booking</a> for the reservation"
            ),
            array(
                "name" => "quantity",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The quantity of reservations to hold"
            ),
            array(
                "name" => "sessionId",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "An optional session identifier to be used for documentation and debugging purposes, typically generated by an external website"
            )
        );
    }
}
