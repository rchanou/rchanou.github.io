<?php

namespace ClubSpeed\Documentation\API;

class DocChecks Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'checks';
        $this->header          = 'Checks';
        $this->url             = 'checks';
        $this->info            = $this->info();

        $this->expand();
        $this->calls['void'] = $this->_void();
        $this->calls['finalize'] = $this->finalize();

        $this->preface = $this->preface();
        $this->json = $this->json();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    A <code class="prettyprint">Check</code> record is a representation of a financial invoice.
</p>
<p>
    Note that <code class="prettyprint">Checks</code> cannot be deleted through typical REST functionality,
    and instead should be <a href="#checks-void">voided</a>.
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
                , 'required'    => true
                , 'description' => 'The primary key for the record'
            )
            , array(
                  'name'        => 'customerId'
                , 'type'        => 'Integer'
                , 'default'     => '0'
                , 'required'    => false
                , 'description' => 'The ID of the <a href="#customers">customer</a> for the check, where available'
            )
            , array(
                  'name'        => 'type'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'required'    => false
                , 'description' => ''
                    ."\n<span>"
                    ."\n  The type of the check"
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
                , 'required'    => false
                , 'description' => ''
                    ."\n<span>"
                    ."\n  The status of the check"
                    ."\n</span>"
                    ."\n<ol start=\"0\">"
                    ."\n  <li>Open</li>"
                    ."\n  <li>Closed</li>"
                    ."\n</ol>"
                    ."\n<span>Note that when creating a new Check record, this will always be set to 0</span>"
            )
            , array(
                  'name'        => 'name'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'The name of the check'
            )
            , array(
                  'name'        => 'userId'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'The ID of the user who created the check'
            )
            , array(
                  'name'        => 'total'
                , 'type'        => 'Double'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'The applied total for the check. This calculated field includes all underlying check details, taxes, discounts, fees, and gratuity'
            )
            , array(
                  'name'        => 'broker'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'The name of the check broker'
            )
            , array(
                  'name'        => 'notes'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'Any additional notes for the check'
            )
            , array(
                  'name'        => 'gratuity'
                , 'type'        => 'Double'
                , 'default'     => '0.00'
                , 'required'    => false
                , 'description' => 'Any additional gratuity to be applied for the whole check'
            )
            , array(
                  'name'        => 'fee'
                , 'type'        => 'Double'
                , 'default'     => '0.00'
                , 'required'    => false
                , 'description' => 'Any additional fee to be applied for the whole check'
            )
            , array(
                  'name'        => 'openedDate'
                , 'type'        => 'DateTime'
                , 'default'     => '{Now}'
                , 'required'    => false
                , 'description' => 'The timestamp on which the check was opened'
            )
            , array(
                  'name'        => 'closedDate'
                , 'type'        => 'DateTime'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'The timestamp on which the check was closed'
            )
            , array(
                  'name'        => 'isTaxExempt'
                , 'type'        => 'Boolean'
                , 'default'     => ''
                , 'required'    => false
                , 'description' => 'An override flag stating whether or not the entire check is exempt from taxation'
            )
            , array(
                  'name'        => 'discount'
                , 'type'        => 'Double'
                , 'default'     => '0.00'
                , 'required'    => false
                , 'description' => 'Any discount to be applied for the whole check'
            )
            , array(
                  'name'        => 'discountNotes'
                , 'type'        => 'String'
                , 'required'    => false
                , 'create'      => 'available'
                , 'description' => 'Any additional notes for the discount'
            )
            , array(
                  'name'        => 'discountUserId'
                , 'type'        => 'Integer'
                , 'required'    => false
                , 'create'      => 'available'
                , 'description' => 'The ID of the user who added the discount'
            )
            , array(
                  'name'        => 'invoiceDate'
                , 'type'        => 'DateTime'
                , 'required'    => false
                , 'create'      => 'available'
                , 'description' => 'The timestamp on which the invoice was handled'
            )
        );
    }

    private function _void() {
        return array(
            'id' => 'void',
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
    This extension method will close the <code class="prettyprint">Check</code>
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

    private function finalize() {
        return array(
            'id' => 'finalize',
            'header' => 'Finalize',
            'header_icon' => 'send',
            'type' => 'create',
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'subroute' => '/:id/finalize'
            ),
            'usage' => <<<EOS
<p>
    This extension method will close the <code class="prettyprint">Check</code>,
    running any additional requirements such as creating and assigning gift cards,
    sending receipt emails, or adding customers and reservations to heats,
    which is pre-determined by the <code class="prettyprint">productType</code>
    of the <code class="prettyprint">Products</code>
    attached through each <code class="prettyprint">CheckDetail</code>.
</p>
<p>
    Note that the balance of the <code class="prettyprint">Check</code> <em>must</em> be <code class="prettyprint">0</code>
    before <code class="prettyprint">finalize</code> can be run.
</p>
<br>
<p>
    One caveat is that if there is a reservation <code class="prettyprint">Product</code>
    attached to the <code class="prettyprint">Check</code>,
    then finalize needs to know to which <code class="prettyprint">Heat</code>
    the purchasing <code class="prettyprint">Customer</code> should be added.
    To do that, post a request body as follows, where <code class="prettyprint">checkDetailId</code>
    is the id of the line item containing the reservation <code class="prettyprint">Product</code>,
    and <code class="prettyprint">heatId</code> is the id of the <code class="prettyprint">Heat</code>
    to which the <code class="prettyprint">Customer</code> should be added.

</p>
<p>
    <strong>This information is required
    for any check which contains a
    reservation <code class="prettyprint">Product</code></strong>.
</p>
<pre class="prettyprint">
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checks/:id/finalize
{
    "details": [
        {
            "checkDetailId": 13385,
            "heatId": 113
        }
    ]
}
</pre>
<br>
<p>
    If you do not wish to have ClubSpeed send receipt emails for you,
    you may pass in the following flag in the response body to disable
    automatic emailing.
</p>
<pre class="prettyprint">
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checks/:id/finalize
{
    "sendCustomerReceiptEmail": false
}
</pre>
<br>
<p>
    In both cases, receipt information will be returned in the response body,
    in the format which follows.
</p>
<pre class="prettyprint">
HTTP/1.1 200 OK
{
    "checkId": 7561,
    "customer": "Jim Joe",
    "email": "bob155711@clubspeed.com",
    "business": "My Karting Business",
    "checkSubtotal": "£10.00",
    "checkTotal": "£10.75",
    "checkTax": "£0.75",
    "balance": "£0.00",
    "details": [{
        "checkDetailId": 13863,
        "note": "Heat #329710 scheduled at 23-03-2016 10:48",
        "productName": "Reservation Product for API Unit Testing",
        "description": "Reservation Product for API Unit Testing: Heat #329710 scheduled at 23-03-2016 10:48",
        "quantity": 5,
        "price": "£2.00",
        "heatId": 329710,
        "scheduledTime": "23-03-2016 10:48",
        "trackName": "Adults "
    }],
    "payments": [{
        "type": "External",
        "amount": "£10.75"
    }]
}
</pre>

EOS
        );
    }
}
