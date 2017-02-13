<?php

namespace ClubSpeed\Documentation\API;

class DocEventTaskTypes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-task-types';
        $this->header  = 'Event Task Types';
        $this->url     = 'eventTaskTypes';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
  Event task types is a container of task types
  to be tracked for completion,
  where the tracking information is stored
  with the <a href="#event-tasks">EventTasks</a>.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "eventTaskTypeId": 1,
    "deleted": false,
    "seq": 2,
    "description": "",
    "name": "Confirmation Email Sent"
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventTaskTypeId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "A soft delete flag for the record"
            ),
            array(
                "name" => "seq",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The sequence in which the task types should appear"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the task type"
            ),
            array(
                "name" => "name",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The name of the task type"
            )
        );
    }
}
