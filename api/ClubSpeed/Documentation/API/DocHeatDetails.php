<?php

namespace ClubSpeed\Documentation\API;

class DocHeatDetails Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'heat-details';
        $this->header          = 'Heat Details';
        $this->url             = 'heatDetails';
        $this->info            = $this->info();
        $this->version         = 'V2';
        $this->json            = $this->json();
        $this->preface         = $this->preface();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "heatId": 3,
  "customerId": 1000001,
  "autoNo": null,
  "lineUpPosition": 2,
  "groupId": 0,
  "proskill": 1200,
  "pointHistoryId": 0,
  "firstTime": true,
  "userId": 5,
  "finishPosition": null,
  "groupFinishPosition": null,
  "proskillDiff": null,
  "positionEditedDate": null,
  "historyAutoNo": null,
  "scores": 0,
  "timeAdded": "2013-11-26T00:18:42.43",
  "assignedtoEntitleHeat": true
}
EOS;
    }

    private function preface() {
        return <<<EOS
<h4> Description </h4>
<p>
    A record in <code class="prettyprint">HeatDetails</code>
    is representative of a <code class="prettyprint">Customer</code>
    being entered into a <code class="prettyprint">Heat</code>.
</p>
<p>
    In order to add a <code class="prettyprint">Customer</code>
    to a <code class="prettyprint">Heat</code>, insert a <code class="prettyprint">HeatDetails</code>
    record containing a primary key pair
    of the <code class="prettyprint">Heat</code> ID and the <code class="prettyprint">Customer</code> ID.
    To remove a <code class="prettyprint">Customer</code> from a <code class="prettyprint">Heat</code>,
    simply delete the <code class="prettyprint">HeatDetails</code> record.
</p>
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "heatId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Part of the composite primary key for the record"
            ),
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Part of the composite primary key for the record"
            ),
            array(
                "name" => "assignedtoEntitleHeat",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "autoNo",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "finishPosition",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "firstTime",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "groupFinishPosition",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "groupId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "historyAutoNo",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "lineUpPosition",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "pointHistoryId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "positionEditedDate",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "proskill",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "proskillDiff",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "scores",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "timeAdded",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            )
        );
    }
}
