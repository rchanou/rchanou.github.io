<?php

namespace ClubSpeed\Documentation\API;

class DocMembershipTypes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'membership-types';
        $this->header  = 'Membership Types';
        $this->url     = 'membershipTypes';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">MembershipType</code>
    contains the relevant definition for a type of membership.
    An instance of this membership type can be given to a customer
    through the use of <code class="prettyprint">Membership.membershipTypeId</code>.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "membershipTypeId": 1,
  "description": "Annual Membership",
  "enabled": true,
  "expirationType": 365,
  "expires": true,
  "priceLevel": 1
  "seq": 1,
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "membershipTypeId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the membership type"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "true",
                "required" => false,
                "description" => "Flag indicating whether this membership type is currently enabled"
            ),
            array(
                "name" => "expirationType",
                "type" => "Integer",
                "default" => "365",
                "required" => false,
                "description" => "The number of days when this membership will expire, where applicable"
            ),
            array(
                "name" => "expires",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "A flag indicating whether or not the membership should ever expire"
            ),
            array(
                "name" => "priceLevel",
                "type" => "Integer",
                "default" => "1",
                "required" => false,
                "description" => "The price level that this membership type would grant to a customer"
            ),
            array(
                "name" => "seq",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The sequence in which the membership types should appear in a dropdown"
            )
        );
    }
}
