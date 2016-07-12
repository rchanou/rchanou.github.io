<?php

namespace ClubSpeed\Documentation\API;

class DocEventHeatDetails Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-heat-details';
        $this->header  = 'Event Heat Details';
        $this->url     = 'eventHeatDetails';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
An <code class="prettyprint">EventHeatDetails</code> record
is a reference to a <code class="prettyprint">Customer</code>
who has been placed in a queue
for a specific <code class="prettyprint">Event</code>.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "eventId": 3,
  "customerId": 1000007,
  "proskill": 1200,
  "added": "2013-05-29T11:35:33.23",
  "roundLoseNum": 0,
  "totalRaces": 1
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "Part of the composite primary key for the record, the id of the <a href=\"#customers\">customer</a> in the queue"
            ),
            array(
                "name" => "eventId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "Part of the composite primary key for the record, the id of the parent <a href=\"#events\">event</a>"
            ),
            array(
                "name" => "dateAdded",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the <a href=\"#customers\">customer</a> was added to the event queue"
            ),
            array(
                "name" => "roundLoseNum",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The round at which the <a href=\"#customers\">customer</a> dropped out of the event, where relevant"
            ),
            array(
                "name" => "proskill",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The proskill for the <a href=\"#customers\">customer</a>"
            ),
            array(
                "name" => "totalRacesInEvent",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The total number of races in the <a href=\"#events\">event</a>"
            )
        );
    }
}
