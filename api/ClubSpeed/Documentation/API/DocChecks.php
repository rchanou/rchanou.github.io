<?php

namespace ClubSpeed\Documentation\API;

class DocChecks Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'checks';
        $this->header          = 'Checks';
        $this->url             = 'checks';
        $this->info            = $this->info();
        // $this->calls['create'] = $this->create();
        // $this->calls['single'] = $this->single();
        // $this->calls['match']  = $this->match();
        // $this->calls['search'] = $this->search();
        // $this->calls['update'] = $this->update(); // this adds a delete section. why???
        // $this->calls['delete'] = $this->delete();

        $this->expand();
        $this->calls['void'] = $this->_void();

        $this->preface = $this->preface();
        $this->json = $this->json();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">Check</code> record is a representation of a financial invoice.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "checks": [
    {
      "checkId": 1234,
      "customerId": 0,
      "type": 1,
      "status": 1,
      "name": "",
      "userId": 6,
      "total": 16,
      "broker": "",
      "notes": "",
      "gratuity": 0,
      "fee": 0,
      "openedDate": "2014-03-22T11:50:28.68",
      "closedDate": "2014-03-22T11:56:04.82",
      "isTaxExempt": false,
      "discount": 0,
      "discountId": 0,
      "discountNotes": "",
      "discountUserId": 0,
      "invoiceDate": null
    }
  ]
}
EOS;
    }

    private function info() {
        return array(
            array(
                  'name'        => 'checkId'
                , 'type'        => 'Integer'
                , 'default'     => '{Generated}'
                , 'update'      => 'unavailable'
                , 'create'      => 'unavailable'
                , 'description' => 'The ID for the check.'
                
            )
            , array(
                  'name'        => 'customerId'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'create'      => 'required'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID of the customer for the check.'
            )
            , array(
                  'name'        => 'type'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => ''
                    ."\n<span>"
                    ."\n  The type of the Check."
                    ."\n</span>"
                    ."\n<ol>"
                    ."\n  <li>Regular</li>"
                    ."\n  <li>Event</li>"
                    ."\n</ol>"
            )
            , array(
                  'name'        => 'status'
                , 'type'        => 'Integer'
                , 'default'     => '0'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => ''
                    ."\n<span>"
                    ."\n  The status of the Check."
                    ."\n</span>"
                    ."\n<ol start=\"0\">"
                    ."\n  <li>Open</li>"
                    ."\n  <li>Closed</li>"
                    ."\n</ol>"
                    ."\n<span>Note that when creating a new Check record, this will always be set to 0.</span>"
            )
            , array(
                  'name'        => 'name'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => 'The name of the check.'
            )
            , array(
                  'name'        => 'userId'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'create'      => 'required'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID of the user who created the check.'
            )
            , array(
                  'name'        => 'total'
                , 'type'        => 'Double'
                , 'default'     => ''
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The applied total for the check. This calculated field includes all underlying check details, taxes, discounts, fees, and gratuity.'
            )
            , array(
                  'name'        => 'broker'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'The name of the check broker.'
            )
            , array(
                  'name'        => 'notes'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'Any additional notes for the check.'
            )
            , array(
                  'name'        => 'gratuity'
                , 'type'        => 'Double'
                , 'default'     => '0.00'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'Any additional gratuity to be applied for the whole check.'
            )
            , array(
                  'name'        => 'fee'
                , 'type'        => 'Double'
                , 'default'     => '0.00'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'Any additional fee to be applied for the whole check.'
            )
            , array(
                  'name'        => 'openedDate'
                , 'type'        => 'DateTime'
                , 'default'     => '{Now}'
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The timestamp on which the check was opened.'
            )
            , array(
                  'name'        => 'closedDate'
                , 'type'        => 'DateTime'
                , 'default'     => ''
                , 'create'      => 'unavailable'
                , 'update'      => 'available'
                , 'description' => 'The timestamp on which the check was closed.'
            )
            , array(
                  'name'        => 'isTaxExempt'
                , 'type'        => 'Boolean'
                , 'default'     => ''
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'An override flag stating whether or not the entire check is exempt from taxation.'
            )
            , array(
                  'name'        => 'discount'
                , 'type'        => 'Double'
                , 'default'     => '0.00'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'Any discount to be applied for the whole check.'
            )
            , array(
                  'name'        => 'discountNotes'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'update'      => 'available'
                , 'create'      => 'available'
                , 'description' => 'Any additional notes for the discount.'
            )
            , array(
                  'name'        => 'discountUserId'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'update'      => 'available'
                , 'create'      => 'available'
                , 'description' => 'The ID of the user who added the discount.'
            )
            , array(
                  'name'        => 'invoiceDate'
                , 'type'        => 'DateTime'
                , 'default'     => ''
                , 'update'      => 'available'
                , 'create'      => 'available'
                , 'description' => 'The timestamp on which the invoice was handled.'
            )
        );
    }

    private function _void() {
        return array(
            'header' => 'Void',
            'header_icon' => 'ban-circle',
            'type' => 'delete',
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'subroute' => '/:id/void'
            ),
            'usage' => <<<EOS
<p>
    This will close the <code class="prettyprint">Check</code>
    and void any existing <code class="prettyprint">CheckDetails</code>.
</p>
<p>
    Any <code class="prettyprint">Payment</code>
    attached to the <code class="prettyprint">Check</code> will remain untouched,
    and should be handled separately.
</p>
EOS
        );
    }

    private function create() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checks HTTP/1.1
{
    "broker": "My Broker Name",
    "customerId": 1000001,
    "discount": 0.00,
    "discountNotes": "No discount applicable for this item",
    "discountUserId": 1,
    "fee": 0.00,
    "gratuity": 2.00,
    "invoiceDate": "2014-09-18",
    "isTaxExempt": false,
    "name": "Test Check",
    "notes": "This check is for API testing",
    "type": 1,
    "userId": 1
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checkId": 2304
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checks/2260 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checks": [
        {
            "checkId": 2260,
            "customerId": 0,
            "type": 1,
            "status": 1,
            "name": "new check name!",
            "userId": 6,
            "total": 120,
            "broker": "",
            "notes": "",
            "gratuity": 0,
            "fee": 0,
            "openedDate": "2014-07-27",
            "closedDate": "2014-07-27",
            "isTaxExempt": false,
            "discount": 0,
            "discountId": 0,
            "discountNotes": "",
            "discountUserId": 0,
            "invoiceDate": null
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
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checks?broker=some%20broker%20name! HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checks": [
        {
            "checkId": 2279,
            "customerId": 1000001,
            "type": 0,
            "status": 0,
            "name": "the check!",
            "userId": 1,
            "total": null,
            "broker": "some broker name!",
            "notes": "Notes!",
            "gratuity": 0,
            "fee": 0,
            "openedDate": "2014-09-08",
            "closedDate": null,
            "isTaxExempt": false,
            "discount": 0,
            "discountId": 0,
            "discountNotes": "",
            "discountUserId": 0,
            "invoiceDate": null
        },
        {
            "checkId": 2280,
            "customerId": 1000001,
            "type": 0,
            "status": 0,
            "name": "the check!",
            "userId": 1,
            "total": null,
            "broker": "some broker name!",
            "notes": "Notes!",
            "gratuity": 0,
            "fee": 0,
            "openedDate": "2014-09-08",
            "closedDate": null,
            "isTaxExempt": false,
            "discount": 0,
            "discountId": 0,
            "discountNotes": "",
            "discountUserId": 0,
            "invoiceDate": null
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
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checks?filter=total%3E800.00 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checks": [
        {
            "checkId": 290,
            "customerId": 1019598,
            "type": 2,
            "status": 1,
            "name": "3 pm elite heats",
            "userId": 5,
            "total": 836,
            "broker": "",
            "notes": "",
            "gratuity": 0,
            "fee": 0,
            "openedDate": "2013-12-19",
            "closedDate": "2014-01-19",
            "isTaxExempt": false,
            "discount": 0,
            "discountId": 0,
            "discountNotes": "",
            "discountUserId": 0,
            "invoiceDate": null
        },
        {
            "checkId": 1523,
            "customerId": 1021671,
            "type": 2,
            "status": 1,
            "name": "grand prix",
            "userId": 5,
            "total": 820,
            "broker": "",
            "notes": "",
            "gratuity": 0,
            "fee": 0,
            "openedDate": "2014-04-24",
            "closedDate": "2014-05-10",
            "isTaxExempt": false,
            "discount": 0,
            "discountId": 0,
            "discountNotes": "",
            "discountUserId": 0,
            "invoiceDate": null
        },
        {
            "checkId": 2013,
            "customerId": 1022441,
            "type": 2,
            "status": 1,
            "name": "enduro 1.5hr ",
            "userId": 5,
            "total": 900,
            "broker": "",
            "notes": "",
            "gratuity": 0,
            "fee": 0,
            "openedDate": "2014-06-28",
            "closedDate": "2014-06-28",
            "isTaxExempt": false,
            "discount": 0,
            "discountId": 0,
            "discountNotes": "",
            "discountUserId": 0,
            "invoiceDate": null
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
            ),
            'examples' => array(
                'request' => <<<EOS
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/checks/2260 HTTP/1.1
{
    "gratuity": 5.00
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
              'header'      => 'Delete'
            , 'header_icon' => 'remove'
            , 'id'          => 'delete'
            , 'type'        => 'delete'
            , 'info'        => array(
                'access' => 'Private',
                'access_icon' => 'lock'
            )
            , 'usage'       => <<<EOS
Check deletes are unavailable!
EOS
        );
    }
}