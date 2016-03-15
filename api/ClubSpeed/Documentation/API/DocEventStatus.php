<?php

namespace ClubSpeed\Documentation\API;

class DocEventStatus Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-statuses';
        $this->header  = 'Event Statuses';
        $this->url     = 'eventStatuses';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "eventStatusId": 1,
  "colorId": -16711936,
  "seq": 1,
  "status": "Unconfirmed"
}
EOS;
    }

    private function preface() {
        return <<<EOS
        <h4>Description</h4>
        <p>
            Event statuses are end-user defined statuses to be assigned to an <code class="prettyprint">EventReservation</code>.
            An <code class="prettyprint">eventStatusId</code>
            should be stored with <code class="prettyprint">EventReservation.status</code> in order to signify
            the current status of an <code class="prettyprint">EventReservation</code>.
        </p>
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventStatusId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "colorId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID of the color which will be used to highlight the event when given this status."
            ),
            array(
                "name" => "seq",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The order in which the event status appears in dropdowns."
            ),
            array(
                "name" => "status",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The readable description of the status."
            )
        );
    }
}
