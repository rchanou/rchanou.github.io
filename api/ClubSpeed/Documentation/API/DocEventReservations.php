<?php

namespace ClubSpeed\Documentation\API;

class DocEventReservations Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-reservations';
        $this->header  = 'Event Reservations';
        $this->url     = 'eventReservations';
        $this->version = 'V2';
        $this->info    = $this->info();
        $this->json    = $this->json();
        $this->preface = $this->preface();

        $this->calls['update-event-status'] = $this->updateEventStatus();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    An <code class="prettyprint">EventReservation</code> is paired
    to an <code class="prettyprint">Event</code> as a way of storing
    and indicating specific reservation values for an <code class="prettyprint">Event</code>. 
</p>
EOS;
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
  For example, assume the following example response:
</p>
<pre class="prettyprint">
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/eventstatuses?order=seq,status HTTP/1.1
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
<br>
<p>
    In order to give an EventReservation a status of "A&D Paid", the following call should be made:
</p>
<pre class="prettyprint">
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/eventreservations/:id HTTP/1.1
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
  "noOfRacers": 22,
  "notes": "Notes!",
  "ptsPerReservation": 1,
  "repId": 3,
  "startTime": "2013-11-26T18:30:00.00",
  "status": 3,
  "subject": "",
  "typeId": 1,
  "userId": 5,
  "externalSystemId": null
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventReservationId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The ID for the event reservation"
            ),
            array(
                "name" => "allowOnlineReservation",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether reservations can be made online."
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
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether or not the reservation has been soft deleted"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description for the event reservation"
            ),
            array(
                "name" => "endTime",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The time at which the event reservation is expected to end"
            ),
            array(
                "name" => "eventTypeId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID for the <a href=\"#event-types\">event type</a> of the <a href=\"#events\">event</a>"
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
            array(
                "name" => "mainId",
                "type" => "int",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID for the parent event reservation, where relevant"
            ),
            array(
                "name" => "minNoOfAdultsPerBooking",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The minimum number of customers per booking"
            ),
            array(
                "name" => "noOfRacers",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The current number of booked customers"
            ),
            // array(
            //     "name" => "noOfTotalRacers",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => "The max number of booked customers"
            // ),
            array(
                "name" => "notes",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The notes for the event reservation"
            ),
            array(
                "name" => "ptsPerReservation",
                "type" => "Integer",
                "default" => "",
                "required" => false,
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
                "required" => false,
                "description" => "The expected start time of the event reservation"
            ),
            array(
                "name" => "status",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The index for the event status of the event reservation. Please see <a href=\"#event-reservations-update-event-status\">here</a> for additional information"
            ),
            array(
                "name" => "subject",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The name for the event reservation"
            ),
            array(
                "name" => "typeId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID for the <a href=\"#event-reservation-types\">event reservation type</a>"
            ),
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID for the user that made the event reservation"
            ),
            array(
                "name" => "externalSystemId",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "A field for storing an external reference for the event reservation. Not utilized internally by ClubSpeed"
            )
        );
    }
}
