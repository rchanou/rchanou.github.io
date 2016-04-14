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
  "finishPosition": null,
  "firstTime": true,
  "groupFinishPosition": null,
  "groupId": 0,
  "lineUpPosition": 2,
  "pointHistoryId": 0,
  "positionEditedDate": null,
  "proskill": 1200,
  "proskillDiff": null,
  "scores": 0,
  "timeAdded": "2013-11-26T00:18:42.43",
  "userId": 5,
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
                "required" => true,
                "description" => "Part of the composite primary key for the record"
            ),
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "Part of the composite primary key for the record"
            ),
            array(
                "name" => "autoNo",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The kart number for the <a href=\"#customers\">customer</a>. Note that this will not be assigned until during / after the <a href=\"#heat-main\">heat</a>"
            ),
            array(
                "name" => "finishPosition",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The finish position for the <a href=\"#customers\">customer</a>"
            ),
            array(
                "name" => "firstTime",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Denormalized flag indicating whether or not this is the first race of the <a href=\"#customers\">customer</a>"
            ),
            array(
                "name" => "groupFinishPosition",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The finish position of the <a href=\"#customers\">customer</a> relative to their group, where applicable"
            ),
            array(
                "name" => "groupId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the group to which the racer belongs, where applicable"
            ),
            array(
                "name" => "lineUpPosition",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The line up position selected for the <a href=\"#customers\">customer</a>"
            ),
            array(
                "name" => "pointHistoryId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the point history utilized to add this <a href=\"#customers\">customer</a> to the <a href=\"#heat-main\">heat</a>, where applicable"
            ),
            array(
                "name" => "positionEditedDate",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The timestamp at which the position was edited"
            ),
            array(
                "name" => "proskill",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The denormalized proskill of the <a href=\"#customers\">customer</a>"
            ),
            array(
                "name" => "proskillDiff",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The amount at which the proskill of the <a href=\"#customers\">customer</a> changed as an outcome of the <a href=\"#heat-main\">heat</a>"
            ),
            array(
                "name" => "timeAdded",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The time at which the <a href=\"#customers\">customer</a> was added to the <a href=\"#heat-main\">heat</a>"
            ),
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the user that added the <a href=\"#customers\">customer</a> to the <a href=\"#heat-main\">heat</a>"
            )
        );
    }
}
