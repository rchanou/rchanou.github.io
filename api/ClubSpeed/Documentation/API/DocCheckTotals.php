<?php

namespace ClubSpeed\Documentation\API;

class DocCheckTotals Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'check-totals';
        $this->header          = 'Check Totals';
        $this->url             = 'checkTotals';
        $this->readonly        = true;
        $this->info            = $this->info();
        $this->calls['create'] = $this->create();
        $this->expand();
        $this->calls['virtual'] = $this->virtual(); // expand virtual after parent -- uses a copy of create's expanded data
    }

    // protected function expand() {
    //     parent::expand();
    // }

    private function virtual() {
        $virtual = (array)($this->calls['create']); // grab a copy
        $virtual['header'] = 'Virtual';
        $virtual['id'] = 'virtual';
        $virtual['info']['url'] .= '/virtual';
        $virtual['info']['subroute'] = '/virtual';
        $virtual['usage'] = <<<EOS
<p>
    The virtual checkTotals call does <strong>not</strong> create a check in the database.
</p>
<p>
    Instead, the virtual call is a way to determine subtotals, taxes, and totals
    without needing to create the underlying check records by posting the same
    data structure for <a href="#check-totals-create">Create</a> to the /virtual route.
</p>
<p>
    If you are unable to map the return data by <code class="prettyprint">productId</code> due to a repeat in product,
    then the details array will also accept fake and temporary <code class="prettyprint">checkDetailId</code> properties
    to assist with mapping the return data.
</p>
<p>
    Please note that the Query Operations for <a href="#query-operations-column-selection">Column Selection</a>
    are available for this query. To add a column to the select list, add the name of the property <em>only</em>.
    This includes any properties which are part of the <code class="prettyprint">details</code> array.
    See the example request and response below for a full example.
</p>
EOS;
        $virtual['examples']['request'] = <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checkTotals/virtual?select=customerId,checkSubtotal,checkTax,checkTotal,checkDetailSubtotal,checkDetailTax,checkDetailTotal,discount,discountApplied HTTP/1.1
{
  "checks": [
    {
      "customerId": 1000001,
      "checkDiscountId": 1,
      "details": [
        {
          "productId": 3,
          "qty": 1
        },
        {
          "productId": 5,
          "qty": 2,
          "checkDetailDiscountId": 5
        }
      ]
    }
  ]
}
EOS;
        $virtual['examples']['response'] = <<<EOS
HTTP/1.1 200 OK
{
  "checks": [
    {
      "checkId": 0,
      "customerId": 1000001,
      "checkSubtotal": 85,
      "checkTax": 17.85,
      "checkTotal": 102.85,
      "discount": 1,
      "details": [
        {
          "checkDetailSubtotal": 15,
          "checkDetailTax": 3.15,
          "checkDetailTotal": 18.15
        },
        {
          "checkDetailSubtotal": 70,
          "checkDetailTax": 14.7,
          "checkDetailTotal": 84.7,
          "discountApplied": 2.3
        }
      ]
    }
  ]
}
EOS;
        return $virtual;
    }

    private function info() {
        return array(
            array(
                  'name'        => 'checks'
                , 'type'        => 'Array<Check>'
                , 'description' => 'The container for check objects'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'Check.customerId'
                , 'type'        => 'Integer'
                , 'description' => 'The id of the <a href="#customers">customer</a> for the <a href="#checks">check</a>'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'Check.userId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the user who is creating the <a href="#checks">check</a>'
                , 'create'      => 'available'
            )
            , array(
                  'name'        => 'Check.checkType'
                , 'type'        => 'Integer'
                , 'description' => 'The type of the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkStatus'
                , 'type'        => 'Integer'
                , 'description' => 'The status of the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.name'
                , 'type'        => 'String'
                , 'description' => 'The name of the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkTotalApplied'
                , 'type'        => 'String'
                , 'description' => 'The total stored on the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.broker'
                , 'type'        => 'String'
                , 'description' => 'The name of the broker for the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.notes'
                , 'type'        => 'String'
                , 'description' => 'The name for the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.gratuity'
                , 'type'        => 'Double'
                , 'description' => 'The gratuity to be applied to the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.fee'
                , 'type'        => 'Double'
                , 'description' => 'The fee to be applied to the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.openedDate'
                , 'type'        => 'Double'
                , 'description' => 'The date on which the <a href="#checks">check</a> was opened'
            )
            , array(
                  'name'        => 'Check.closedDate'
                , 'type'        => 'Double'
                , 'description' => 'The date on which the <a href="#checks">check</a> was closed'
            )
            , array(
                  'name'        => 'Check.isTaxExempt'
                , 'type'        => 'Boolean'
                , 'description' => 'The override for the tax exemption of the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.discount'
                , 'type'        => 'Decimal'
                , 'description' => 'The calculated <a href="#discount-types">discount</a> amount which was applied to the <a href="#checks">check</a>. Note that this is <strong>not</strong> a summation which includes the check detail discounts. That information will be reflected in the check total'
            )
            , array(
                  'name'        => 'Check.checkDiscountId'
                , 'type'        => 'Integer'
                , 'description' => 'The <a href="#discount-types">discount</a> for the <a href="#checks">check</a>'
                , 'create'      => 'available'
            )
            , array(
                  'name'        => 'Check.checkDiscountNotes'
                , 'type'        => 'String'
                , 'description' => 'The notes for the <a href="#discount-types">discount</a> on the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkDiscountUserId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the user that applied the <a href="#discount-types">discount</a> to the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkSubtotal'
                , 'type'        => 'Double'
                , 'description' => 'The calculated subtotal for the entire <a href="#checks">check</a>. Note that this calculation will use <i>live</i> <a href="#products">product</a> values'
            )
            , array(
                  'name'        => 'Check.checkTax'
                , 'type'        => 'Double'
                , 'description' => 'The calculated tax for the entire <a href="#checks">check</a>. Note that this calculation will use <i>live</i> <a href="#taxes">tax</a> values'
            )
            , array(
                  'name'        => 'Check.checkTotal'
                , 'type'        => 'Double'
                , 'description' => 'The calculated total for the entire <a href="#checks">check</a>. Note that this calculation will use <i>live</i> <a href="#taxes">tax</a> and <a href="#products">product</a> values'
            )
            , array(
                  'name'        => 'Check.checkPaidTax'
                , 'type'        => 'Double'
                , 'description' => 'The amount of tax which has already been paid for this <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkPaidTotal'
                , 'type'        => 'Double'
                , 'description' => 'The amount of tax which has already been paid for this <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkRemainingTax'
                , 'type'        => 'Double'
                , 'description' => 'The remaining tax to be paid for this <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.checkRemainingTotal'
                , 'type'        => 'Double'
                , 'description' => 'The remaining total to be paid for this <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'Check.details'
                , 'type'        => 'Array<CheckDetail>'
                , 'description' => 'The container for check detail objects'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailId'
                , 'type'        => 'Integer'
                , 'description' => 'The unique identifier for the <a href="#check-details">check detail</a>, which represents a line item for the <a href="#checks">check</a>'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailStatus'
                , 'type'        => 'Integer'
                , 'description' => 'The status for the <a href="#check-details">check detail</a>'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailType'
                , 'type'        => 'Integer'
                , 'description' => 'The type of the <a href="#check-details">check detail</a>'
            )
            , array(
                  'name'        => 'CheckDetail.productId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the <a href="#products">product</a> for the <a href="#check-details">check detail</a>'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'CheckDetail.productName'
                , 'type'        => 'Integer'
                , 'description' => 'The name of the <a href="#products">product</a>'
            )
            , array(
                  'name'        => 'CheckDetail.qty'
                , 'type'        => 'Integer'
                , 'description' => 'The quantity of the <a href="#products">product</a> for the <a href="#check-details">check detail</a>'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailDiscountId'
                , 'description' => 'The id for the <a href="#discount-type">discount</a> which was applied to the <a href="#check-details">check detail</a>'
                , 'type'        => 'Integer'
                , 'create'      => 'available'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailDiscountUserId'
                , 'description' => 'The id for the <a href="#users">user</a> who applied the <a href="#check-details">check detail</a> <a href="#discount-types">discount</a>'
                , 'type'        => 'Integer'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailDiscountDesc'
                , 'type'        => 'String'
                , 'description' => 'The description for the <a href="#check-details">check detail</a> <a href="#discount-types">discount</a>'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailDiscountCalculateType'
                , 'type'        => 'String'
                , 'description' => 'The calculation type for the <a href="#check-details">check detail</a> <a href="#discount-types">discount</a>'
            )
            , array(
                  'name'        => 'CheckDetail.discountApplied'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated amount of the <a href="#discount-types">discount</a> applied to this <a href="#check-details">check detail</a>'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailSubtotal'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated subtotal for the <a href="#check-details">check detail</a> items. Note that this calculation will use <i>live</i> <a href="#products">product</a> values'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailTax'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated tax for the <a href="#check-details">check detail</a> items. Note that this calculation will use <i>live</i> <a href="#taxes">tax</a> values'
            )
            , array(
                  'name'        => 'CheckDetail.checkDetailTotal'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated total for the <a href="#check-details">check detail</a> items. Note that this calculation will use <i>live</i> <a href="#taxes">tax</a> and <a href="#products">product</a> values'
            )
        );
    }

    private function create() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'usage' => <<<EOS
<p>
    While <code class="prettyprint">CheckTotals</code> is technically a read-only
    set of <code class="prettyprint">Check</code> information from the database,
    posting to /checkTotals is available as an extension method
    to dynamically create and populate all relevant <code class="prettyprint">Check</code> and <code class="prettyprint">CheckDetails</code> records
    by using the <code class="prettyprint">customerId</code>, <code class="prettyprint">productId</code>,
    and <code class="prettyprint">qty</code> fields.
</p>
<p>
    To apply a discount at the <code class="prettyprint">Check</code> level,
    include a <code class="prettyprint">checkDiscountId</code>
    which corresponds to a <code class="prettyprint">Discount</code>.
</p>
<p>
    To apply a discount at the <code class="prettyprint">Check Detail</code> level,
    include a <code class="prettyprint">checkDetailDiscountId</code>,
    which again corresponds to a <code class="prettyprint">Discount</code>.
</p>
<p>
    Note that if you apply a <code class="prettyprint">Check</code> level
    <code class="prettyprint">Discount</code> through this endpoint,
    then make modifications to related <code class="prettyprint">Check Details</code>
    by deleting, adding, or modifying existing line items,
    the <code class="prettyprint">Check</code> level <code class="prettyprint">Discount</code>
    will <strong>not</strong> be recalculated, and instead will still reflect
    the original amount which was calculated. As such, care should be taken to
    apply any <code class="prettyprint">Discounts</code> as part of the last
    operation before accepting payment and closing the check.
</p>
EOS
            , 'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checkTotals?debug=1 HTTP/1.1
{
  "checks": [
    {
      "customerId": 1000001,
      "checkDiscountId": 1,
      "details": [
        {
          "productId": 3,
          "qty": 1
        },
        {
          "productId": 5,
          "qty": 2,
          "checkDetailDiscountId": 5
        }
      ]
    }
  ]
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "checkId": 2361
}
EOS
            )
        );
    }

    private function single() {
        return array(
            'info' => array(
                'access' => 'private'
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkTotals/123 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "checks": [
    {
      "customerId": 0,
      "checkId": 123,
      "checkType": "1",
      "checkStatus": 1,
      "name": "",
      "userId": 6,
      "checkTotalApplied": 3,
      "broker": "",
      "notes": "",
      "gratuity": 0,
      "fee": 0,
      "openedDate": "2013-12-01T11:59:54",
      "closedDate": "2013-12-01T12:00:19",
      "isTaxExempt": false,
      "discount": 0,
      "checkSubtotal": 3,
      "checkTax": 0,
      "checkTotal": 3,
      "checkPaidTax": 0,
      "checkPaidTotal": 3,
      "checkRemainingTax": 0,
      "checkRemainingTotal": 0,
      "details": [
        {
          "checkDetailId": 464,
          "checkDetailStatus": 3,
          "checkDetailType": 1,
          "productId": 7,
          "productName": "Balaclava",
          "createdDate": "2013-12-01T11:59:54",
          "qty": 1,
          "unitPrice": 3,
          "unitPrice2": 0,
          "discountApplied": 0,
          "taxId": 1,
          "taxPercent": 0,
          "voidNotes": "",
          "cId": null,
          "vId": null,
          "bonusValue": null,
          "paidValue": null,
          "comValue": null,
          "entitle1": null,
          "entitle2": null,
          "entitle3": null,
          "entitle4": null,
          "entitle5": null,
          "entitle6": null,
          "entitle7": null,
          "entitle8": null,
          "m_Points": null,
          "m_CustId": null,
          "m_OldMembershiptypeId": null,
          "m_NewMembershiptypeId": null,
          "m_Days": null,
          "m_PrimaryMembership": null,
          "p_PointTypeId": null,
          "p_Points": null,
          "p_CustId": null,
          "r_Points": null,
          "discountUserId": null,
          "discountDesc": null,
          "calculateType": null,
          "discountId": null,
          "discountNotes": null,
          "g_Points": null,
          "g_CustId": null,
          "gst": 0,
          "m_DaysAdded": null,
          "s_SaleBy": null,
          "s_NoOfLapsOrSeconds": null,
          "s_CustId": null,
          "s_Vol": null,
          "cadetQty": 0,
          "checkDetailSubtotal": 3,
          "checkDetailTax": 0,
          "checkDetailTotal": 3
        }
      ]
    }
  ]
}
EOS
            )
        );
    }

    private function match() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails?qty=5&productId=43 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checkDetails": [
        {
            "checkDetailId": 2564,
            "checkId": 645,
            "status": 3,
            "type": 4,
            "productId": 43,
            "productName": "Online 20 Min Arrive n Drive",
            "createdDate": "2014-01-23",
            "qty": 5,
            "cadetQty": 0
        },
        {
            "checkDetailId": 5254,
            "checkId": 1537,
            "status": 3,
            "type": 4,
            "productId": 43,
            "productName": "Online 20 Min Arrive n Drive",
            "createdDate": "2014-04-25",
            "qty": 5,
            "cadetQty": 0
        }
    ]
}
EOS
            )
        );
    }

    private function search() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails?filter=3%3CqtyANDqty%3C%3D5ANDcreatedDate%3E2014-08-01 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checkDetails": [
        {
            "checkDetailId": 7556,
            "checkId": 2288,
            "status": 1,
            "type": 1,
            "productId": 8,
            "productName": "",
            "createdDate": "2014-09-15",
            "qty": 5,
            "cadetQty": 0
        },
        {
            "checkDetailId": 7557,
            "checkId": 2288,
            "status": 1,
            "type": 1,
            "productId": 8,
            "productName": "",
            "createdDate": "2014-09-15",
            "qty": 5,
            "cadetQty": 0
        }
    ]
}
EOS
            )
        );
    }

    private function update() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            // , 'available'     => array(
            //       'cadetQty'
            //     , 'qty'
            //     , 'status'
            //     , 'type'
            // )
            // , 'unavailable' => array(
            //       'checkId'
            //     , 'createdDate'
            //     , 'productName'
            // )
            , 'examples' => array(
                'request' => <<<EOS
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails/7564 HTTP/1.1
{
    "status": 2,
    "type": 2, 
    "productId": 11,
    "qty": 0,
    "cadetQty": 3
}
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }

    private function delete() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
DELETE https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails/7560 HTTP/1.1
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }
}