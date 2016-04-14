<?php

namespace ClubSpeed\Documentation\API;

class DocMemberships Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'memberships';
        $this->header  = 'Memberships';
        $this->url     = 'memberships';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">Membership</code> is a pairing
    of <code class="prettyprint">Customer</code> and <code class="prettyprint">MembershipType</code>,
    representing a membership which the customer either has or had up until the expiration date.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "customerId": 1000001,
  "membershipTypeId": 105,
  "byUserId": 1
  "changed": "2014-06-17T00:00:00.00",
  "expiration": "2015-06-17T00:00:00.00",
  "notes": "",
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "Part of the composite primary key for the record, references a <a href=\"#customers\">customer</a>"
            ),
            array(
                "name" => "membershipId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "Part of the composite primary key for the record, references a <a href=\"#membership-types\">membership type</a>"
            ),
            array(
                "name" => "byUserId",
                "type" => "Integer",
                "default" => "1",
                "required" => false,
                "description" => "The ID of the user that created the membership"
            ),
            array(
                "name" => "changed",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the membership last changed"
            ),
            array(
                "name" => "expiration",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the membership will expire"
            ),
            array(
                "name" => "notes",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Any notes for the membership"
            )
        );
    }
}
