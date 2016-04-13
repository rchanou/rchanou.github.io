<?php

namespace ClubSpeed\Documentation\API;

class DocEvents Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'events';
        $this->header  = 'Events';
        $this->url     = 'events';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
        $this->expand();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    An <code class="prettyprint">Event</code> is the parent record
    for all event specific information.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "eventId": 1,
  "createdHeatSpots": 2,
  "createdHeatTime": "2013-11-25T23:12:42.87",
  "eventDesc": "sprinter",
  "eventDuration": 30,
  "eventNotes": "",
  "eventScheduledTime": "2013-11-25T23:30:00.00",
  "eventTheme": -16776961,
  "eventTypeId": 1,
  "eventTypeName": "Sprint Race",
  "memberOnly": false,
  "reservationId": 0,
  "totalRacers": 2,
  "trackNo": 1
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "createdHeatSpots",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The number of heat spots which have been created for this event"
            ),
            array(
                "name" => "createdHeatTime",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The time at which the relevant heat will start"
            ),
            array(
                "name" => "eventDesc",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description for the event"
            ),
            array(
                "name" => "eventDuration",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The expected duration of the event in minutes"
            ),
            array(
                "name" => "eventNotes",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The notes for the event"
            ),
            array(
                "name" => "eventScheduledTime",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The scheduled time for the event"
            ),
            array(
                "name" => "eventTheme",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The theme for the event"
            ),
            array(
                "name" => "eventTypeId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID for the <a href=\"#event-types\">type</a> of the event"
            ),
            array(
                "name" => "eventTypeName",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The name of the <a href=\"#event-types\">type</a> of the event"
            ),
            array(
                "name" => "memberOnly",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether entry into the event should require a membership"
            ),
            array(
                "name" => "reservationId",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => "The ID for the <a href=\"#event-reservations\">reservation</a> container for the event"
            ),
            array(
                "name" => "totalRacers",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The total number of racers for the event"
            ),
            array(
                "name" => "trackNo",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The number of the track to be used for the event"
            )
        );
    }
}
