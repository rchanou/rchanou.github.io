<?php

namespace ClubSpeed\Documentation\API;

class DocEventHeatTypes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-heat-types';
        $this->header  = 'Event Heat Types';
        $this->url     = 'eventHeatTypes';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
  <code class="prettyprint">Event Heat Types</code> contains information grouped by
  <code class="prettyprint">Event Type</code> designed to be a lookup for creating
  <code class="prettyprint">Event Rounds</code> when creating an
  <code class="prettyprint">Event</code>.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "eventHeatTypeId": 1,
    "cadetsPerHeat": 0,
    "description": "5 Minute Qualifying",
    "eventTypeId": 1,
    "heatsPerRacer": 1,
    "heatTypeId": 17,
    "racersPerHeat": 22,
    "roundNumber": 1
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventHeatTypeId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "cadetsPerHeat",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The maximum number of cadet racers for an event round"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the event round"
            ),
            array(
                "name" => "eventTypeId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The <a href=\"#event-types\">event type</a> for this default event round information"
            ),
            // array(
            //     "name" => "gridId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "heatsPerRacer",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The number of heats in which each racer will participate"
            ),
            array(
                "name" => "heatTypeId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The <a href=\"#heat-types\">heat type</a> to be used with the resulting <a href=\"#heat-main\">heat</a>"
            ),
            // array(
            //     "name" => "isInverted",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "isSemi",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "lineUpType",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "proceedMethod",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "proceedValue",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "racersPerHeat",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The maximum number of racers for this event round"
            ),
            array(
                "name" => "roundNumber",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ordered round number in the <a href=\"#events\">event</a>"
            ),
            // array(
            //     "name" => "roundWinby",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "scoreSystemId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "teamGroupingType",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // )
        );
    }
}
