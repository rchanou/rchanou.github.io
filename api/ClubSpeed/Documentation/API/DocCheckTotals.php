<?php

namespace ClubSpeed\Documentation\API;

class DocCheckTotals Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'check-totals';
        $this->header = 'Check Totals';
        $this->url = 'checkTotals';
        $this->info = $this->info();
        // $this->calls['virtual'] = $this->virtual();
        $this->calls['create'] = $this->create();
        // $this->calls['single'] = $this->single();
        // $this->calls['match']  = $this->match(); // leave match out for now?
        // $this->calls['search'] = $this->search(); // leave search out for now?
        // $this->calls['update'] = $this->update();
        // $this->calls['delete'] = $this->delete();
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
    The virtual namespaced checkTotals POST does not create a check in the database.
</p>
<p>
    Instead, the virtual call is a way to determine subtotals, taxes, and totals
    without needing to create the underlying check records by posting the same
    data structure for <a href="#check-totals-create">Create</a> to the /virtual route.
</p>
<p>
    Please note that the Query Operations for <a href="#query-operations-column-selection">Column Selection</a>
    are available for this query.
</p>
EOS;
        $virtual['examples']['request'] = <<<EOS
POST https://mytrack.clubspeedtiming.com/api/index.php/checkTotals/virtual?select=customerId,%20checkSubtotal,%20checkTax,%20checkTotal,%20checkDetailSubtotal,%20checkDetailTax,%20checkDetailTotal HTTP/1.1
Content-Length: 220
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
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
Date: Thu, 25 Sep 2014 22:05:28 GMT
Content-Length: 443
Content-Type: application/json
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
            // , array(
            //       'name'        => 'detail.MORE_TO_DO'
            //     , 'type'        => 'Integer'
            //     , 'description' => 'TODO: DETERMINE WHICH OTHER COLUMNS TO SHOWO SHOW HERE'
            //     , 'create'      => 'required'
            // )
            
            // , array(
            //       'name'        => 'checks.details.checkDetailsId'
            //     , 'type'        => 'Integer'
            //     , 'default'     => ''
            //     , 'description' => 'The ID of the CheckDetails record.'
            //     , 'update'      => 'unavailable'
            //     , 'create'      => 'unavailable'
            // )
            // , array(
            //       'name'        => 'checkId'
            //     , 'type'        => 'Integer'
            //     , 'default'     => ''
            //     , 'description' => 'The ID of the Checks record.'
            // )
            // , array(
            //       'name'        => 'productName'
            //     , 'type'        => 'String'
            //     , 'default'     => '{Calculated}'
            //     , 'description' => 'The name for the underlying product.'
            // )
            // , array(
            //       'name'        => 'status'
            //     , 'type'        => 'Integer'
            //     , 'default'     => '1'
            //     , 'description' => ""
            //         ."\n<span>"
            //         ."\n  The status of the CheckDetails."
            //         ."\n</span>"
            //         ."\n<ol>"
            //         ."\n  <li>IsNew</li>"
            //         ."\n  <li>HasVoided</li>"
            //         ."\n  <li>CannotDeleted</li>"
            //         ."\n</ol>"
            //         ."\n<span>Note that when creating a new CheckDetails record, this will always be set to 1.</span>"
            // )
            // , array(
            //       'name'        => 'type'
            //     , 'type'        => 'Integer'
            //     , 'default'     => '1'
            //     , 'description' => ''
            //         ."\n<span>"
            //         ."\n  The type for the CheckDetails."
            //         ."\n</span>"
            //         ."\n<ol>"
            //         ."\n  <li>RegularItem</li>"
            //         ."\n  <li>PointItem</li>"
            //         ."\n  <li>FoodItem</li>"
            //         ."\n  <li>ReservationItem</li>"
            //         ."\n  <li>GameCardItem</li>"
            //         ."\n  <li>MembershipItem</li>"
            //         ."\n  <li>GiftCardItem</li>"
            //         ."\n  <li>EntitleItem</li>"
            //         ."\n</ol>"
            // )
            // , array(
            //       'name'        => 'qty'
            //     , 'type'        => 'Integer'
            //     , 'icon'        => 'warning-sign orange'
            //     , 'description' => 'The quantity of the product to be added to check details. Either qty or cadetQty must be provided and greater than zero.'
            // )
            // , array(
            //       'name'        => 'cadetQty'
            //     , 'type'        => 'Integer'
            //     , 'icon'        => 'warning-sign orange'
            //     , 'description' => 'The cadet quantity of the product to be added to check details. Either qty or cadetQty must be provided and greater than zero.'
            // )
            // , array(
            //       'name'        => 'createdDate'
            //     , 'type'        => 'DateTime'
            //     , 'default'     => '{Date.Now}'
            //     , 'description' => 'The timestamp indicating when the CheckDetails record was created.'
            // )
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
POST https://mytrack.clubspeedtiming.com/api/index.php/checkTotals?debug=1 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Content-Length: 239
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
Date: Thu, 25 Sep 2014 21:52:34 GMT
Content-Length: 21
Content-Type: application/json
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
GET https://mytrack.clubspeedtiming.com/api/index.php/checkDetails/7556 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 20:33:32 GMT
Content-Length: 245
Content-Type: application/json
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
GET https://mytrack.clubspeedtiming.com/api/index.php/checkDetails?qty=5&productId=43 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 21:03:01 GMT
Content-Length: 521
Content-Type: application/json
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
GET https://mytrack.clubspeedtiming.com/api/index.php/checkDetails?filter=3%3CqtyANDqty%3C%3D5ANDcreatedDate%3E2014-08-01 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 21:22:00 GMT
Content-Length: 683
Content-Type: application/json
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
PUT https://mytrack.clubspeedtiming.com/api/index.php/checkDetails/7564 HTTP/1.1
Content-Length: 86
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
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
Date: Mon, 15 Sep 2014 22:42:24 GMT
Content-Length: 0
Content-Type: text/html
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
DELETE https://mytrack.clubspeedtiming.com/api/index.php/checkDetails/7560 HTTP/1.1
Content-Length: 0
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 23:35:16 GMT
Content-Length: 0
Content-Type: text/html
EOS
            )
        );
    }
}