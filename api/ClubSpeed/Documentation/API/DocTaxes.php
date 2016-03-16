<?php

namespace ClubSpeed\Documentation\API;

class DocTaxes Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'taxes';
        $this->header  = 'Taxes';
        $this->url     = 'taxes';
        $this->info    = $this->info();
        $this->version = 'V1';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">Tax</code> record is a way to define how tax calculations should be applied,
    typically to a <code class="prettyprint">Product</code>. 
</p>
<p>
    To use a <code class="prettyprint">Tax</code> record, <code class="prettyprint">Tax.taxId</code>
    should be linked by setting it as <code class="prettyprint">Product.taxId</code>,
    and the values will be automatically calculated
    during check calculation time.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "taxes": [
    {
      "taxId": 2,
      "description": "Sales Taxes",
      "amount": 6.25,
      "deleted": false,
      "gst": 0
    }
  ]
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "taxId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "amount",
                "type" => "Double",
                "default" => "",
                "required" => true,
                "description" => "The percentage of the tax, where <code class=\"prettyprint\">6.25</code> would indicate 6.25% percent"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "false",
                "create" => "available",
                "update" => "available",
                "description" => "A flag indicating whether the tax has been soft deleted. For historical purposes, the tax should <em>not</em> be hard deleted"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "A description for the tax"
            ),
            array(
                "name" => "gst",
                "type" => "Double",
                "default" => "0.0",
                "create" => "available",
                "update" => "available",
                "description" => "The percentage of the tax which should be considered GST, where applicable"
            )
        );
    }
}
