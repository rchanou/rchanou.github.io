<?php

namespace ClubSpeed\Documentation\API;

class DocEventTasks Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-tasks';
        $this->header  = 'Event Tasks';
        $this->url     = 'eventTasks';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
  A list of the tasks to be completed for a given event reservation.
  If a task has been completed, completedBy and completedAt will not be null.
  Information such as task name and description can be found with the
  <a href="#event-task-types">EventTaskTypes</a> records.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "eventTaskId": 128,
    "eventReservationId": 28,
    "completedBy": 1,
    "completedAt": "2016-01-01T00:00:00.00",
    "eventTaskTypeId": 4
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventTaskId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "eventReservationId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => 'The id of the <a href="#event-reservations">event reservation</a>'
            ),
            array(
                "name" => "completedBy",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => 'The id of the <a href="#users">user</a> that completed the task'
            ),
            array(
                "name" => "completedAt",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the task was completed"
            ),
            array(
                "name" => "eventTaskTypeId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The id of the <a href=\"#event-task-types\">event task type</a> to be completed"
            )
        );
    }
}
