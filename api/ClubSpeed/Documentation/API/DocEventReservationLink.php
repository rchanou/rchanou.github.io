<?php

namespace ClubSpeed\Documentation\API;

class DocEventReservationLink Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-reservation-links';
        $this->header  = 'Event Reservation Links';
        $this->url     = 'eventReservationLinks';
        $this->version = 'V2';
        $this->preface = $this->preface();
        $this->info    = $this->info();
        $this->json    = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "eventReservationLinkId": 1,
  "checkId": 4,
  "reservationId": 1
}
EOS;
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
An <code class="prettyprint">EventReservationLink</code> record 
is a cross reference allowing specific <code class="prettyprint">Check</code> records to be linked to
specific <code class="prettyprint">EventReservation</code> records, which in turn are linked to specific <code class="prettyprint">Events</code>.
</p>
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventReservationLinkId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "checkId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for a check linked to a reservation."
            ),
            array(
                "name" => "reservationId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the event reservation."
            )
        );
    }
}
