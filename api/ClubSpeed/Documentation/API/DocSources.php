<?php

namespace ClubSpeed\Documentation\API;

class DocSources Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'sources';
        $this->header  = 'Sources';
        $this->url     = 'sources';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">Source</code> is linked to a <code class="prettyprint">Customer</code>
    by way of <code class="prettyprint">Customer.howdidyouhearaboutus</code>.
    A <code class="prettyprint">Source</code> is a way to declare
    how a <code class="prettyprint">Customer</code> could find and register at a venue,
    such as the result of a marketing campaign, word of mouth, newspaper ad, etc.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "sourceId": 1,
  "deleted": false,
  "enabled": true,
  "locationId": 1
  "name": "Radio Advertisement",
  "seq": 9,
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "sourceId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            // array(
            //     "name" => "caboOnly",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether the source has been soft deleted"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "true",
                "required" => false,
                "description" => "Flag indicating whether or not the source is currently enabled"
            ),
            // array(
            //     "name" => "languages",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "locationId",
                "type" => "Integer",
                "default" => "1",
                "required" => false,
                "description" => "The ID of the venue location which this source applies to, where applicable"
            ),
            array(
                "name" => "name",
                "type" => "String",
                "default" => "",
                "required" => true,
                "description" => "The name of the source"
            ),
            array(
                "name" => "seq",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The sequence in which the sources should display in a dropdown"
            )
        );
    }
}
