<?php

namespace ClubSpeed\Documentation\API;

class DocDiscountType Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'discount-types';
        $this->header  = 'Discount Types';
        $this->url     = 'discountType';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    <code class="prettyprint">Discount Types</code> contains a list
    of discounts which can be utilized by the Check Totals
    <a href="#check-totals-create">Create</a> and
    <a href="#check-totals-virtual">Virtual</a> API calls.
    <br>
    Note that these automated calculations of discounts are only available
    with the Check Totals endpoints, and they will not be automatically calculated
    when attempting to work with the Checks or Check Details endpoints.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "discountId": 1,
    "description": "$1 Race Discount",
    "calculateType": 1,
    "amount": 1,
    "enabled": false,
    "needApproved": false,
    "productClassId": 1,
    "deleted": true
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "discountId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "amount",
                "type" => "Double",
                "default" => "",
                "required" => false,
                "description" => "The amount of the discount. See <code class=\"prettyprint\">calculateType</code> for usage"
            ),
            array(
                "name" => "calculateType",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                'description' => ''
                  ."\n<p>"
                  ."\n  The calculation type for the discount. This determines how <code class=\"prettyprint\">amount</code> is to be considered and used for calculations"
                  ."\n</p>"
                  ."\n<ol>"
                  ."\n  <li>Amount</li>"
                  ."\n  <li>Percentage</li>"
                  ."\n</ol>"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether the discount has been soft deleted"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description for the discount"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether the discount type is currently enabled"
            ),
            array(
                "name" => "needApproved",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether the discount should require manager approval"
            ),
            array(
                "name" => "productClassId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The <a href=\"#product-classes\">product class</a> for which the discount should be applicable. Note that this is not hard enforced by the API"
            ),
            // array(
            //     "name" => "transferId",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // )
        );
    }
}
