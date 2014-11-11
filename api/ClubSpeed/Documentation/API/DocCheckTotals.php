<?php

namespace ClubSpeed\Documentation\API;

class DocCheckTotals Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id                  = 'check-totals';
        $this->header              = 'Check Totals';
        $this->url                 = 'checkTotals';
        $this->info                = $this->info();
        $this->calls['create']     = $this->create();
        $this->calls['single']     = $this->single();
        // $this->calls['match']   = $this->match(); // leave match out for now?
        // $this->calls['search']  = $this->search(); // leave search out for now?
        $this->expand();
    }

    protected function expand() {
        parent::expand();
        $this->calls['virtual'] = $this->virtual(); // expand virtual after parent -- uses a copy of create's expanded data
    }

    private function virtual() {
        $virtual = (array)($this->calls['create']); // grab a copy
        $virtual['header'] = 'Virtual';
        $virtual['id'] = 'virtual';
        $virtual['info']['url'] .= '/virtual';
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
    If you are unable to map the return data by productId due to a repeat in product,
    then the details array will also accept fake and temporary checkDetailIds
    to assist with mapping the return data.
</p>
<p>
    Please note that the Query Operations for <a href="#query-operations-column-selection">Column Selection</a>
    are available for this query.
</p>
EOS;
        $virtual['examples']['request'] = <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checkTotals/virtual?select=customerId,%20checkSubtotal,%20checkTax,%20checkTotal,%20checkDetailSubtotal,%20checkDetailTax,%20checkDetailTotal HTTP/1.1
{
  "checks": [
    {
      "customerId": 1000001,
      "details": [
        {
          "productId": 3,
          "qty": 1
        },
        {
          "productId": 5,
          "qty": 2
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
      "details": [
        {
          "checkDetailSubtotal": 15,
          "checkDetailTax": 3.15,
          "checkDetailTotal": 18.15
        },
        {
          "checkDetailSubtotal": 70,
          "checkDetailTax": 14.7,
          "checkDetailTotal": 84.7
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
                , 'type'        => 'Array<Checks>'
                , 'description' => 'The container for Checks objects.'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'check.customerId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the check\'s customer.'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'check.userId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the user who is creating the check.'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'check.checkType'
                , 'type'        => 'Integer'
                , 'description' => 'The type of the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkStatus'
                , 'type'        => 'Integer'
                , 'description' => 'The status of the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.name'
                , 'type'        => 'String'
                , 'description' => 'The name of the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkTotalApplied'
                , 'type'        => 'String'
                , 'description' => 'The total stored on the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.broker'
                , 'type'        => 'String'
                , 'description' => 'The name of the broker for the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.notes'
                , 'type'        => 'String'
                , 'description' => 'The name for the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.gratuity'
                , 'type'        => 'Double'
                , 'description' => 'The gratuity to be applied to the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.fee'
                , 'type'        => 'Double'
                , 'description' => 'The fee to be applied to the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.openedDate'
                , 'type'        => 'Double'
                , 'description' => 'The date on which the Check was opened.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.closedDate'
                , 'type'        => 'Double'
                , 'description' => 'The date on which the Check was closed.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.isTaxExempt'
                , 'type'        => 'Boolean'
                , 'description' => 'The override for the tax exemption of the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.discount'
                , 'type'        => 'Double'
                , 'description' => 'The discount to be applied to the Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkSubtotal'
                , 'type'        => 'Double'
                , 'description' => 'The calculated subtotal for the entire Check. Note that this calculation will use <i>live</i> product values.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkTax'
                , 'type'        => 'Double'
                , 'description' => 'The calculated tax for the entire Check. Note that this calculation will use <i>live</i> tax values.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkTotal'
                , 'type'        => 'Double'
                , 'description' => 'The calculated total for the entire Check. Note that this calculation will use <i>live</i> tax and product values.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkPaidTax'
                , 'type'        => 'Double'
                , 'description' => 'The amount of tax which has already been paid for this Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkPaidTotal'
                , 'type'        => 'Double'
                , 'description' => 'The amount of tax which has already been paid for this Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkRemainingTax'
                , 'type'        => 'Double'
                , 'description' => 'The remaining tax to be paid for this Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.checkRemainingTotal'
                , 'type'        => 'Double'
                , 'description' => 'The remaining total to be paid for this Check.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'check.details'
                , 'type'        => 'Array<CheckDetails>'
                , 'description' => 'The container for CheckDetails objects.'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'detail.checkDetailId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the CheckDetails record.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'detail.checkDetailStatus'
                , 'type'        => 'Integer'
                , 'description' => 'The status for the CheckDetails record.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'detail.checkDetailType'
                , 'type'        => 'Integer'
                , 'description' => 'The type of the CheckDetails record.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'detail.productId'
                , 'type'        => 'Integer'
                , 'description' => 'The id for the Product for the CheckDetails record.'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'detail.productName'
                , 'type'        => 'Integer'
                , 'description' => 'The name of the Product.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'detail.qty'
                , 'type'        => 'Integer'
                , 'description' => 'The quantity of the Product for the CheckDetails record.'
                , 'create'      => 'required'
            )
            , array(
                  'name'        => 'detail.checkDetailSubtotal'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated subtotal for the CheckDetails items. Note that this calculation will use <i>live</i> product values.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'detail.checkDetailTax'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated tax for the CheckDetails items. Note that this calculation will use <i>live</i> tax values.'
                , 'create'      => 'unavailable'
            )
            , array(
                  'name'        => 'detail.checkDetailTotal'
                , 'type'        => 'Integer'
                , 'description' => 'The calculated total for the CheckDetails items. Note that this calculation will use <i>live</i> tax and product values.'
                , 'create'      => 'unavailable'
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
    While CheckTotals is a mapping to a read-only set of info to the database,
    posting to CheckTotals is a method to create underlying Checks and CheckDetails records
    with one API call.
</p>
<p>
    Note that the id for the Check record will be the only id returned when using this call,
    and CheckDetails ids will not be returned here.
</p>
EOS
            , 'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checkTotals?debug=1 HTTP/1.1
{
  "checks": [
    {
      "customerId": 1000001,
      "userId": 1,
      "details": [
        {
          "productId": 3,
          "qty": 1
        },
        {
          "productId": 5,
          "qty": 2
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