<?php

namespace ClubSpeed\Documentation\API;

class DocEventReservationTypes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-reservation-types';
        $this->header  = 'Event Reservation Types';
        $this->url     = 'eventReservationTypes';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
  Event Reservation Types holds a description for the reservation type.
</p>
<p>
  Note that an Event Reservation Type can be considered a grouping, or header,
  for what appears on the ClubSpeed event calendars.
  These descriptions may correspond to resources at the venue,
  including resources such as party rooms.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
[
  {
    "eventReservationTypeId": 1,
    "description": "Indoor Track"
  },
  {
    "eventReservationTypeId": 2,
    "description": "Outdoor Track"
  },
  {
    "eventReservationTypeId": 3,
    "description": "Party Room 1"
  }
]
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventReservationTypeId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the event reservation type"
            )
            // array(
            //     "name" => "picture",
            //     "type" => "",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // )
        );
    }
}
