<?php

namespace ClubSpeed\Documentation\API;

class DocHeatTypes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'heat-types';
        $this->header  = 'Heat Types';
        $this->url     = 'heatTypes';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A HeatType is treated as a default template 
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "heatTypesId": 1,
  "cannotBelow": 35000,
  "cannotExceed": 1000000,
  "cost": 10,
  "deleted": false,
  "enabled": true,
  "isEventHeatOnly": false,
  "isPracticeHeat": false,
  "lapsOrMinutes": 600,
  "memberOnly": false,
  "name": "Arrive and Drive 10 Min",
  "raceBy": 0,
  "racersPerHeat": 10,
  "scheduleDuration": 10,
  "speedLevel": 1,
  "trackId": 1,
  "winBy": 0
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "heatTypesId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            // array(
            //     "name" => "cadetsPerHeat",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "cannotBelow",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The minimum cut off for lap times (in milliseconds)"
            ),
            array(
                "name" => "cannotExceed",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The maximum cut off for lap times (in milliseconds)"
            ),
            array(
                "name" => "cost",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => "The number of points required for this heat type, where applicable"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether this heat type has been soft deleted"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "true",
                "required" => false,
                "description" => "Flag indicating whether this heat type is currently enabled"
            ),
            // array(
            //     "name" => "entitleHeat",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "heatTypeName",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The name of the heat type"
            ),
            array(
                "name" => "isEventHeatOnly",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether this heat type is meant only for events"
            ),
            array(
                "name" => "isPracticeHeat",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether this heat type is meant to be a practice round"
            ),
            array(
                "name" => "lapsOrMinutes",
                "type" => "Integer",
                "default" => "10",
                "required" => false,
                "description" => "Quantity of laps or minutes (depending on the heat type) required for the heat type to finish"
            ),
            array(
                "name" => "memberOnly",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether a heat type should only allow entrance to members"
            ),
            // array(
            //     "name" => "onHeatFinishAssignLoop",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "onHeatStart",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "onHeatStop",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "printResult",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "productId", // => productId is not everywhere yet, related to the QuickPOS.
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "raceBy",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => ""
                    ."\n<span>"
                    ."\n  The indication of whether the heat type should treat the value at <code class=\"prettyprint\">lapsOrMinutes</code> as laps or minutes"
                    ."\n</span>"
                    ."\n<ol start=0>"
                    ."\n  <li>Minutes</li>"
                    ."\n  <li>Laps</li>"
                    ."\n</ol>"
            ),
            array(
                "name" => "racersPerHeat",
                "type" => "Integer",
                "default" => "12",
                "required" => false,
                "description" => "Total number of racers available for the heat type"
            ),
            array(
                "name" => "scheduleDuration",
                "type" => "Integer",
                "default" => "12",
                "required" => false,
                "description" => "The expected duration of the heat type"
            ),
            array(
                "name" => "speedLevel",
                "type" => "Integer",
                "default" => "1",
                "required" => false,
                "description" => "The speed level for the heat type"
            ),
            array(
                "name" => "trackId",
                "type" => "Integer",
                "default" => "NULL",
                "required" => false,
                "description" => "The default track ID for the heat type"
            ),
            // array(
            //     "name" => "web",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => "Flag indicating whether this heat type should be "
            // ),
            array(
                "name" => "winBy",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => ""
                    ."\n<span>"
                    ."\n  The indication of whether the heat is won by laps or position"
                    ."\n</span>"
                    ."\n<ol start=0>"
                    ."\n  <li>Best Time</li>"
                    ."\n  <li>Finish Position</li>"
                    ."\n</ol>"
            )
        );
    }
}
