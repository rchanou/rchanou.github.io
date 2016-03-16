<?php

namespace ClubSpeed\Documentation\API;

class DocProductClasses Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'product-classes';
        $this->header  = 'Product Classes';
        $this->url     = 'productClasses';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    <code class="prettyprint">ProductClasses</code> are the records which determine how
    a <code class="prettyprint">Product</code> should be aggregated in the reporting system.
</p>
<p>
    To apply a <code class="prettyprint">ProductClass</code> to a <code class="prettyprint">Product</code>,
    set <code class="prettyprint">Product.productClassId</code>
    to be <code class="prettyprint">ProductClass.productClassId</code>, and all reports will update accordingly.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "productClassId": 16,
  "description": "Karting",
  "deleted": false,
  "exportName": "kartexport_123"
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "productClassId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "A flag indicating whether this record has been soft deleted. For reporting purposes, the product classes should <em>not</em> be hard deleted."
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the product class"
            ),
            array(
                "name" => "exportName",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The export name to be used when building exports from reports"
            )
        );
    }
}
