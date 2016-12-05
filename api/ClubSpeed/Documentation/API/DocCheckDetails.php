<?php

namespace ClubSpeed\Documentation\API;

class DocCheckDetails Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'check-details';
        $this->header  = 'Check Details';
        $this->url     = 'checkDetails';
        $this->info    = $this->info();
        $this->preface = $this->preface();
        $this->json    = $this->json();
    }

        private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">CheckDetail</code> record represents a line item and quantity
    attached to a <code class="prettyprint">Check</code>.
</p>
<p>
    To attach a new line item to a check, insert a <code class="prettyprint">CheckDetail</code>
    using the relevant <code class="prettyprint">checkId</code>, <code class="prettyprint">productId</code>, and <code class="prettyprint">qty</code>,
    typically allowing the API to auto-populate the remainder of the information from the <code class="prettyprint">Product</code> definition.
</p>
stuff
EOS;
    }

    private function json() {
        return <<<EOS
{
  "checkDetails": [
    {
      "checkDetailId": 12863,
      "checkId": 6779,
      "status": 1,
      "type": 1,
      "productId": 1002,
      "productName": "Rotax Helmet Case",
      "createdDate": "2015-09-08T09:17:05.00",
      "qty": 1,
      "unitPrice": 30000,
      "unitPrice2": 0,
      "discountApplied": 0,
      "taxId": 1,
      "taxPercent": 0,
      "gst": 0,
      "voidNotes": "",
      "p_Points": 0,
      "p_CustId": null,
      "r_Points": null,
      "discountUserId": null,
      "discountDesc": null,
      "calculateType": null,
      "discountId": null,
      "discountNotes": null,
      "g_Points": 0,
      "g_CustId": null,
      "m_DaysAdded": null,
      "s_SaleBy": null,
      "s_NoOfLapsOrSeconds": null,
      "s_CustId": null,
      "s_Vol": null
    }
  ]
}
EOS;
    }

    // private function info() {
    //     return array(
    //         array(
    //               'name'        => 'checkDetailsId'
    //             , 'type'        => 'Integer'
    //             , 'default'     => '{Generated}'
    //             , 'create'      => 'unavailable'
    //             , 'update'      => 'unavailable'
    //             , 'required'    => true
    //             , 'description' => 'The primary key for the record'
    //         )
    //         , array(
    //               'name'        => 'checkId'
    //             , 'type'        => 'Integer'
    //             , 'default'     => ''
    //             , 'create'      => 'required'
    //             , 'update'      => 'unavailable'
    //             , 'required'    => true
    //             , 'description' => 'The ID of the parent <a href="#checks">check</a>'
    //         )
    //         , array(
    //               'name'        => 'productId'
    //             , 'type'        => 'Integer'
    //             , 'default'     => ''
    //             , 'create'      => 'required'
    //             , 'update'      => 'unavailable'
    //             , 'required'    => true
    //             , 'description' => 'The ID of the <a href="#products">product</a> for the check detail'
    //         )
    //         , array(
    //               'name'        => 'productName'
    //             , 'type'        => 'String'
    //             , 'default'     => '{Lookup}'
    //             , 'create'      => 'unavailable'
    //             , 'update'      => 'unavailable'
    //             , 'description' => 'The name for the <a href="#products">product</a> for the check detail'
    //         )
            // , array(
            //       'name'        => 'status'
            //     , 'type'        => 'Integer'
            //     , 'default'     => '1'
            //     , 'create'      => 'available'
            //     , 'update'      => 'available'
            //     , 'description' => ""
            //         ."\n<span>"
            //         ."\n  The status of the check detail"
            //         ."\n</span>"
            //         ."\n<ol>"
            //         ."\n  <li>New</li>"
            //         ."\n  <li>Voided</li>"
            //         ."\n  <li>Permanent</li>"
            //         ."\n</ol>"
            //         ."\n<span>Note that when creating a new check detail, this will always be set to 1.</span>"
            // )
    //         , array(
    //               'name'        => 'type'
    //             , 'type'        => 'Integer'
    //             , 'default'     => '1'
    //             , 'create'      => 'available'
    //             , 'update'      => 'available'
    //             , 'description' => ''
    //                 ."\n<span>"
    //                 ."\n  The type for the <a href=\"#products\">product</a> attached to the check detail"
    //                 ."\n</span>"
    //                 ."\n<ol>"
    //                 ."\n  <li>Regular</li>"
    //                 ."\n  <li>Point</li>"
    //                 ."\n  <li>Food</li>"
    //                 ."\n  <li>Reservation</li>"
    //                 ."\n  <li>GameCard</li>"
    //                 ."\n  <li>Membership</li>"
    //                 ."\n  <li>Gift Card</li>"
    //                 ."\n  <li>Entitle</li>"
    //                 ."\n</ol>"
    //         )
    //         , array(
    //               'name'        => 'qty'
    //             , 'type'        => 'Integer'
    //             , 'icon'        => 'warning-sign orange'
    //             , 'create'      => 'available'
    //             , 'update'      => 'available'
    //             , 'default'     => '0'
    //             , 'description' => 'The quantity of the product to be added to check details. Either qty or cadetQty must be provided and greater than zero.'
    //         )
    //         , array(
    //               'name'        => 'cadetQty'
    //             , 'type'        => 'Integer'
    //             , 'icon'        => 'warning-sign orange'
    //             , 'create'      => 'available'
    //             , 'update'      => 'available'
    //             , 'default'     => '0'
    //             , 'description' => 'The cadet quantity of the product to be added to check details. Either qty or cadetQty must be provided and greater than zero.'
    //         )
    //         , array(
    //               'name'        => 'createdDate'
    //             , 'type'        => 'DateTime'
    //             , 'default'     => '{Date.Now}'
    //             , 'create'      => 'unavailable'
    //             , 'update'      => 'unavailable'
    //             , 'description' => 'The timestamp indicating when the check detail was created'
    //         )
    //     );
    // }

        private function info() {
        return array(
            array(
                "name" => "checkDetailId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            // array(
            //     "name" => "bonusValue",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "cadetQty",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "calculateType",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The type of application for an applied discount"
            ),
            array(
                "name" => "checkId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The ID for the parent <a href=\"#checks\">check</a> of the check detail"
            ),
            // array(
            //     "name" => "cid",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "comValue",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "createdBy",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "createdDate",
                "type" => "DateTime",
                "default" => "{Now}",
                "required" => false,
                "description" => "The timestamp at which the check detail was created"
            ),
            // array(
            //     "name" => "createdOn",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => "The "
            // ),
            array(
                "name" => "discountApplied",
                "type" => "Double",
                "default" => "",
                "required" => false,
                "description" => "The amount of the discount which was applied"
            ),
            array(
                "name" => "discountDesc",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The description of the discount which was applied"
            ),
            array(
                "name" => "discountId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the discount which was applied"
            ),
            array(
                "name" => "discountNotes",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Notes regarding the application of the discount"
            ),
            array(
                "name" => "discountUserId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID for the user who applied the discount"
            ),
            // array(
            //     "name" => "entitle1",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle2",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle3",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle4",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle5",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle6",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle7",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle8",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "g_CustId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the <a href=\"#customers\">customer</a> on which to apply points on purchase. Note that this ID may reference a gift card"
            ),
            array(
                "name" => "g_Points",
                "type" => "Double",
                "default" => "",
                "description" => "The amount of money to be given to <code class=\"prettyprint\">CheckDetail.g_CustId</code> at purchase / <a href=\"#checks-finalize\">check finalize</a>. This value typically corresponds to <code class=\"prettyprint\">Product.g_Points</code>"
            ),
            array(
                "name" => "gst",
                "type" => "Double",
                "default" => "",
                "required" => false,
                "description" => "The percent of the <a href=\"#taxes\">tax</a> to be applied as GST, which corresponds to <code class=\"prettyprint\">Tax.gst</code>"
            ),
            // array(
            //     "name" => "mCustId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mDays",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mDaysAdded",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mNewMembershiptypeId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mOldMembershiptypeId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mPoints",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mPrimaryMembership",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "p_CustId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The ID of the <a href=\"#customers\">customer</a> on which to apply points on purchase"
            ),
            array(
                "name" => "p_Points",
                "type" => "Double",
                "default" => "",
                "required" => false,
                "description" => "The number of points to be applied on purchase, which corresponds to <code class=\"prettyprint\">Product.p_Points</code>"
            ),
            // array(
            //     "name" => "pPointTypeId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "paidValue",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "productId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The ID for the <a href=\"#products\">product</a> on the check detail"
            ),
            array(
                "name" => "productName",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The name of the <a href=\"#products\">product</a> on the check detail"
            ),
            array(
                "name" => "qty",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The quantity of the <a href=\"#products\">product</a> on the check detail"
            ),
            array(
                "name" => "r_Points",
                "type" => "Double",
                "default" => "",
                "required" => false,
                "description" => "The number of reservation points to be applied on purchase, which corresponds to <code class=\"prettyprint\">Product.r_Points</code>"
            ),
            // array(
            //     "name" => "sCustId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sNoOfLapsOrSeconds",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "s_SaleBy",
            //     "type" => "Integer",
            //     "default" => "",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sVol",
            //     "type" => "Integer",
            //     "default" => "",
            //     "description" => ""
            // ),
           array(
                  "name"        => "status"
                , "type"        => "Integer"
                , "default"     => "1"
                , "create"      => "available"
                , "update"      => "available"
                , "description" => ""
                    ."\n<span>"
                    ."\n  The status of the check detail"
                    ."\n</span>"
                    ."\n<ol>"
                    ."\n  <li>New</li>"
                    ."\n  <li>Voided</li>"
                    ."\n  <li>Permanent</li>"
                    ."\n</ol>"
                    ."\n<span>Note that when creating a new check detail, this will always be set to 1.</span>"
            ),
            array(
                "name" => "taxId",
                "type" => "Integer",
                "default" => "",
                "description" => "The ID for the <a href=\"#taxes\">tax</a> to be applied"
            ),
            array(
                "name" => "taxPercent",
                "type" => "Double",
                "default" => "",
                "description" => "The percent of the <a href=\"#taxes\">tax</a> to be applied, which corresponds to <code class=\"prettyprint\">Tax.amount</code>"
            ),
            array(
                "name" => "type",
                "type" => "Integer",
                "default" => "",
                'description' => ''
                    ."\n<span>"
                    ."\n  The type of the <a href=\"#products\">product</a>, which corresponds to <code class=\"prettyprint\">Product.productType</code>"
                    ."\n</span>"
                    ."\n<ol>"
                    ."\n  <li>Regular</li>"
                    ."\n  <li>Point</li>"
                    ."\n  <li>Food</li>"
                    ."\n  <li>Reservation</li>"
                    ."\n  <li>GameCard</li>"
                    ."\n  <li>Membership</li>"
                    ."\n  <li>Gift Card</li>"
                    ."\n  <li>Entitle</li>"
                    ."\n</ol>"
            ),
            array(
                "name" => "unitPrice",
                "type" => "Double",
                "default" => "",
                "description" => "The unitPrice of the <a href=\"#products\">product</a>, which corresponds to <code class=\"prettyprint\">Product.price1</code>"
            ),
            // array(
            //     "name" => "unitPrice2",
            //     "type" => "Double",
            //     "default" => "",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "vid",
            //     "type" => "String",
            //     "default" => "",
            //     "description" => ""
            // ),
            array(
                "name" => "voidNotes",
                "type" => "String",
                "default" => "",
                "description" => "Any notes which were added while voiding the check detail"
            )
        );

    }
}