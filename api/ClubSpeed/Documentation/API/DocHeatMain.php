<?php

namespace ClubSpeed\Documentation\API;

class DocHeatMain Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'heat-main';
        $this->header          = 'Heat Main';
        $this->url             = 'heatMain';
        $this->info            = $this->info();
        $this->version         = 'V2';
        $this->json            = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "heatId": 2,
  "track": 1,
  "scheduledTime": "2013-11-26T00:15:00.00",
  "type": 1,
  "lapsOrMinutes": 600,
  "status": 0,
  "eventRound": null,
  "beginning": null,
  "finish": null,
  "winBy": 0,
  "raceBy": 0,
  "scheduleDuration": 10,
  "pointsNeeded": 10,
  "speedLevel": 1,
  "heatColor": -2302756,
  "numberOfReservation": 7,
  "memberOnly": false,
  "notes": "",
  "scoreId": 0,
  "racersPerHeat": 16,
  "numberOfCadetReservation": 0,
  "cadetsPerHeat": 0
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "heatId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "beginning",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "cadetsPerHeat",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "eventRound",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "finish",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "heatColor",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "notes",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "status",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "type",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "lapsOrMinutes",
                "type" => "Integer",
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
                "name" => "numberOfCadetReservation",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "numberOfReservation",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "pointsNeeded",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "raceBy",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "racersPerHeat",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "scheduledTime",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "scheduleDuration",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "scoreId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "speedLevel",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "track",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "winBy",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            )
        );
    }
}
