<?php

namespace ClubSpeed\Documentation\API;

class DocEventTypes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'event-types';
        $this->header  = 'Event Types';
        $this->url     = 'eventTypes';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "eventTypeId": 1,
  "deleted": false,
  "description": "Sprint Race",
  "displayAtRegistration": true,
  "enabled": true,
  "eventTypeName": "Sprint Race",
  "eventTypeTheme": -20304,
  "memberOnly": false,
  "onlineProductId": 0,
  "ptsPerReservation": 1,
  "trackId": 1
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "eventTypeId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether the record has been soft deleted"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The description of the event type"
            ),
            array(
                "name" => "displayAtRegistration",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether the event type should show during registration"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether or not the event type is currently enabled"
            ),
            array(
                "name" => "eventTypeName",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The name of the event type"
            ),
            array(
                "name" => "eventTypeTheme",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The theme of the event type"
            ),
            array(
                "name" => "memberOnly",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether or not a membership is required for this event type"
            ),
            array(
                "name" => "onlineProductId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID of the product which is used to purchase this event"
            ),
            array(
                "name" => "ptsPerReservation",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The number of points required per reservation"
            ),
            array(
                "name" => "trackId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID of the track for which this event can be added"
            )
        );
    }
}
