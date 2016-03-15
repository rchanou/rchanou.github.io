<?php

namespace ClubSpeed\Documentation\API;

class DocEvents Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'events';
        $this->header          = 'Events';
        $this->url             = 'events';
        $this->info            = $this->info();
        $this->version         = 'V2';
        $this->json            = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "eventId": 1,
  "eventTypeId": 1,
  "memberOnly": false,
  "eventTheme": -16776961,
  "totalRacers": 2,
  "eventDesc": "sprinter",
  "eventTypeName": "Sprint Race",
  "eventDuration": 30,
  "eventScheduledTime": "2013-11-25T23:30:00.00",
  "displayAtRegistration": true,
  "checkId": 0,
  "isEventClosure": false,
  "roundNum": 1,
  "eventNotes": "",
  "reservationId": 0,
  "onlineCode": "",
  "trackNo": 1,
  "createdHeatSpots": 2,
  "createdHeatTime": "2013-11-25T23:12:42.87",
  "totalCadetRacers": 0
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventId",
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
                "description" => ""
            ),
            array(
                "name" => "createdHeatSpots",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "createdHeatTime",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "displayAtRegistration",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventDesc",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventDuration",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventNotes",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventScheduledTime",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventTheme",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventTypeId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventTypeName",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "isEventClosure",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "memberOnly",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "onlineCode",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "reservationId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "roundNum",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "totalCadetRacers",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "totalRacers",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "trackNo",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            )
        );
    }
}
