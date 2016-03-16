<?php

namespace ClubSpeed\Documentation\API;

class DocEventReservations Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'event-reservations';
        $this->header          = 'Event Reservations';
        $this->url             = 'eventReservations';
        $this->version         = 'V2';
        $this->info            = $this->info();
        $this->json            = $this->json();
        $this->expand();

        $this->calls['update-event-status'] = $this->updateEventStatus();
    }

    private function updateEventStatus() {
        $rootUrl = $this->root . $this->url;
        return array(
              'header'      => 'Update Event Status'
            , 'header_icon' => 'pencil'
            , 'id'          => 'update-event-status'
            , 'type'        => 'update'
            , 'info' => array(
                  'url'         => $rootUrl . '/:id'
                , 'verb'        => 'PUT'
                , 'verb_icon'   => 'pencil'
                , 'access'      => 'Private'
                , 'access_icon' => 'lock'
                , 'subroute'    => '/:id'
            )
            , 'usage' => <<<EOS
<p>
    The <code class="prettyprint">EventReservation</code> expects a linked
    <code class="prettyprint">EventReservation.status</code> to be in a specific format.
    Namely, the <code class="prettyprint">status</code> field should be set to be the <strong>index</strong> of the given
    <code class="prettyprint">EventStatus</code>
    when ordered by <code class="prettyprint">EventStatus.seq ASC, EventStatus.status ASC</code>.
</p>
<p>
  For example, assume the following example response from <code class="prettyprint">GET /eventstatuses?order=seq,status</code>
</p>
<pre class="prettyprint">
[
  {
    "eventStatusId": 1,
    "colorId": -16711898,
    "seq": 1,
    "status": "Race Paid"
  },
  {
    "eventStatusId": 2,
    "colorId": -37120,
    "seq": 3,
    "status": "A&D Paid"
  }
]
</pre>
<p>
    In order to give an EventReservation a status of "A&D Paid", the following call should be made:
</p>
<pre class="prettyprint">
PUT /eventreservations/:id
{
    "status": 1
}
</pre>
<p>
    Take special note that the <code class="prettyprint">EventReservation.status</code>
    value is of the <strong>array index</strong> of the original return,
    and <em>not</em> the same value as the <code class="prettyprint">EventStatus.eventStatusId</code>.
</p>
EOS
        );
    }

    private function json() {
        return <<<EOS
{
  "eventReservationId": 1,
  "allowOnlineReservation": false,
  "deleted": true,
  "description": "Reservation description",
  "endTime": "2013-11-26T19:00:00.00",
  "eventTypeId": 1,
  "isEventClosure": false,
  "isMixed": null,
  "mainId": null,
  "minNoOfAdultsPerBooking": 0,
  "minNoOfCadetsPerBooking": 0,
  "noOfCadetRacers": 0,
  "noOfRacers": 22,
  "noOfTotalRacers": 22,
  "notes": "Notes!",
  "ptsPerReservation": 1,
  "repId": 3,
  "startTime": "2013-11-26T18:30:00.00",
  "status": 3,
  "subject": "",
  "typeId": 1,
  "userId": 5
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventReservationId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the event reservation"
            ),
            array(
                "name" => "allowOnlineReservation",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Whether or not reservations can be made online."
            ),
            // array(
            //     "name" => "checkId",
            //     "type" => "int",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => "The ID for the event check"
            // ),
            // array(
            //     "name" => "customerId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => "The ID for the customer who made the reservation"
            // ),
            // array(
            //     "name" => "customerName",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => "The name for the customer who made the reservation"
            // ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether or not the reservation has been soft deleted"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The description for the event reservation"
            ),
            array(
                "name" => "endTime",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The time at which the event reservation is expected to end"
            ),
            array(
                "name" => "eventTypeId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the type of the event"
            ),
            // array(
            //     "name" => "isEventClosure",
            //     "type" => "int",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "isMixed",
            //     "type" => "int",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "label",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => "The label for the event"
            // ),
            // array(
            //     "name" => "mainId",
            //     "type" => "int",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "minNoOfAdultsPerBooking",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The minimum number of adults per booking"
            ),
            array(
                "name" => "minNoOfCadetsPerBooking",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The minimum number of cadets per booking"
            ),
            array(
                "name" => "noOfCadetRacers",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The current number of booked cadets"
            ),
            array(
                "name" => "noOfRacers",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The current number of booked racers"
            ),
            array(
                "name" => "noOfTotalRacers",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The current number of booked racers and cadets"
            ),
            array(
                "name" => "notes",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The notes for the event reservation"
            ),
            array(
                "name" => "ptsPerReservation",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The number of points required to make a reservation"
            ),
            // array(
            //     "name" => "repId",
            //     "type" => "int",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "startTime",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The expected start time of the event"
            ),
            array(
                "name" => "status",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the event status of the event reservation"
            ),
            array(
                "name" => "subject",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The name for the event"
            ),
            array(
                "name" => "typeId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the type of the event reservation"
            ),
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the user that made the event reservation"
            )
        );
    }
}
