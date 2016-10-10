<?php

namespace ClubSpeed\Documentation\API;

class DocEventRounds Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-rounds';
        $this->header  = 'Event Rounds';
        $this->url     = 'eventRounds';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    An <code class="prettyprint">Event Round</code> is the link between
    <code class="prettyprint">Events</code> and their
    corresponding rounds in the <code class="prettyprint">Heat Main</code>,
    through <code class="prettyprint">HeatMain.eventRound</code>.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "eventRoundId": 31,
    "cadetsPerHeat": 0,
    "eventId": 15,
    "heatDescription": "10 Minute Qualifying",
    "heatsPerRacer": 1,
    "heatTypeId": 1,
    "racersPerHeat": 22,
    "roundNumber": 1
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventRoundId",
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
                "description" => "The maximum number of cadet racers for this event round"
            ),
            array(
                "name" => "eventId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The id for the parent <a href=\"#events\">event</a>"
            ),
            // array(
            //     "name" => "gridId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "heatDescription",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the <a href=\"#heat-main\">heat</a>"
            ),
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
                "description" => "The <a href=\"#heat-types\">heat type</a> of the <a href=\"#heat-main\">heat</a>"
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
            //     "name" => "roundWinBy",
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
            // )
        );
    }
}
