<?php

namespace ClubSpeed\Documentation\API;

class DocHeatMain Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'heat-main';
        $this->header  = 'Heat Main';
        $this->url     = 'heatMain';
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
  A <code class="prettyprint">HeatMain</code> record represents an instance of a race on the standard venue calendar. This resource may be utilized
  to look up base information about races such as begin and finish times, or to add or remove races from the calendar.
</p>        
EOS;
    }

    private function json() {
        return <<<EOS
{
  "heatId": 2,
  "beginning": null,
  "eventRound": null,
  "finish": null,
  "heatColor": -2302756,
  "lapsOrMinutes": 600,
  "memberOnly": false,
  "notes": "",
  "numberOfReservation": 7,
  "pointsNeeded": 10,
  "raceBy": 0,
  "racersPerHeat": 16
  "scheduledTime": "2013-11-26T00:15:00.00",
  "scheduleDuration": 10,
  "speedLevel": 1,
  "status": 0,
  "track": 1,
  "type": 1,
  "winBy": 0,
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "heatId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "beginning",
                "type" => "DateTime",
                "default" => "",
                "description" => "The actual start time of the heat"
            ),
            // array(
            //     "name" => "cadetsPerHeat",
            //     "type" => "Integer",
            //     "default" => "{Lookup}"
            //     "description" => "The maximum number of cadet races which can enter the heat, defaults to a lookup from <code class=\"prettyprint\">type</code>"
            // ),
            array(
                "name" => "eventRound",
                "type" => "Integer",
                "default" => "",
                "description" => "The <a href=\"event-rounds\">event round</a> which corresponds to the heat, where applicable"
            ),
            array(
                "name" => "finish",
                "type" => "DateTime",
                "default" => "",
                "description" => "The actual finish time of the heat"
            ),
            array(
                "name" => "heatColor",
                "type" => "Integer",
                "default" => "",
                "description" => "An integer representation of the color to be used on the heat calendar"
            ),
            array(
                "name" => "lapsOrMinutes",
                "type" => "Integer",
                "default" => "{Lookup}",
                "description" => "Quantity of laps or minutes (depending on the heat type) required for the heat to finish, defaults to a lookup from <code class=\"prettyprint\">type</code>"
            ),
            array(
                "name" => "memberOnly",
                "type" => "Boolean",
                "default" => "",
                "description" => "Flag indicating whether a heat should only allow entrance to members"
            ),
            array(
                "name" => "notes",
                "type" => "String",
                "default" => "",
                "description" => "Notes for the heat"
            ),
            // array(
            //     "name" => "numberOfCadetReservation",
            //     "type" => "Integer",
            //     "default" => "0",
            //     "description" => "Number of additional cadet reservaitons for the heat"
            // ),
            array(
                "name" => "numberOfReservation",
                "type" => "Integer",
                "default" => "0",
                "description" => "Number of additional reservations for the heat. Note that these typically represent purchased slots in a heat. The sum of this field and the count of <a href=\"#heat-details\">heat detail</a> records can be considered the number of slots used for the heat"
            ),
            array(
                "name" => "pointsNeeded",
                "type" => "Integer",
                "default" => "",
                "description" => "Number of points required to enter the heat, where applicable. Set this value to 0 where not applicable"
            ),
            array(
                "name" => "raceBy",
                "type" => "Integer",
                "default" => "{Lookup}",
                "description" => ""
                    ."\n<span>"
                    ."\n  The indication of whether the heat will treat the value at <code class=\"prettyprint\">lapsOrMinutes</code> as laps or minutes, defaults to a lookup from <code class=\"prettyprint\">type</code>"
                    ."\n</span>"
                    ."\n<ol start=0>"
                    ."\n  <li>Minutes</li>"
                    ."\n  <li>Laps</li>"
                    ."\n</ol>"
            ),
            array(
                "name" => "racersPerHeat",
                "type" => "Integer",
                "default" => "{Lookup}",
                "description" => "Total number of racers available for the heat, defaults to a lookup from <code class=\"prettyprint\">type</code>"
            ),
            array(
                "name" => "scheduledTime",
                "type" => "DateTime",
                "default" => "",
                "required" => true,
                "description" => "The scheduled start time of the heat"
            ),
            array(
                "name" => "scheduleDuration",
                "type" => "Integer",
                "default" => "{Lookup}",
                "description" => "The expected duration of the heat, defaults to a lookup from <code class=\"prettyprint\">type</code>"
            ),
            // array(
            //     "name" => "scoreId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "description" => ""
            // ),
            array(
                "name" => "speedLevel",
                "type" => "Integer",
                "default" => "",
                "description" => "The speed level for the heat"
            ),
            array(
                "name" => "status",
                "type" => "Integer",
                "default" => "0",
                "description" => ""
                    ."\n<span>"
                    ."\n  The status of the heat"
                    ."\n</span>"
                    ."\n<ol start=0>"
                    ."\n  <li>Open</li>"
                    ."\n  <li>Racing</li>"
                    ."\n  <li>Finished</li>"
                    ."\n  <li>Aborted</li>"
                    ."\n  <li>Closed</li>"
                    ."\n</ol>"
            ),
            array(
                "name" => "track",
                "type" => "Integer",
                "default" => "1",
                "description" => "The ID for the track for the heat"
            ),
            array(
                "name" => "type",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The <a href=\"#heat-types\">heat type</a> for the heat"
            ),
            array(
                "name" => "winBy",
                "type" => "Integer",
                "default" => "{Lookup}",
                "description" => ""
                    ."\n<span>"
                    ."\n  The indication of whether the heat is won by laps or position, defaults to a lookup from <code class=\"prettyprint\">type</code>"
                    ."\n</span>"
                    ."\n<ol start=0>"
                    ."\n  <li>Best Time</li>"
                    ."\n  <li>Finish Position</li>"
                    ."\n</ol>"
            )
        );
    }
}
