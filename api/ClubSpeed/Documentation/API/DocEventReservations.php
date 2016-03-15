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
  "label": 3,
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
  "status": 0,
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
            array(
                "name" => "label",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The label for the event"
            ),
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
                "description" => "The subject for the event"
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
