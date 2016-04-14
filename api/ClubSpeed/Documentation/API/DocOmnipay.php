<?php

namespace ClubSpeed\Documentation\API;

class DocOmnipay Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id                              = 'omnipay';
        $this->header                          = 'Omnipay';
        $this->url                             = 'omnipay';
        $this->calls['omnipay-list-vendors']   = $this->listVendors();
        $this->calls['omnipay-accept-payment'] = $this->acceptPayment();
        $this->calls['omnipay-complete-payment'] = $this->completePayment();
        $this->expand();
    }

    private function listVendors() {
        return array(
          'type' => 'get'
          , 'info' => array(
              'verb' => 'GET'
              , 'verb_icon' => 'save'
              , 'url' => '/api/index.php/' . $this->url
          )
          , 'id' => 'list-vendors'
          , 'header' => 'List Vendors'
          , 'header_icon' => 'save'
          , 'usage' => <<<EOS
<p>
  ClubSpeed utilizes a subset of vendors available through
  the <a href="https://github.com/thephpleague/omnipay">Omnipay</a> library.
  To get a list of these vendors, call the method detailed here.
</p>
<p>
  The response will be an array of objects containing available payment processor information,
  which will each contain <code class="prettyprint">name</code>,
  <code class="prettyprint">type</code>,
  and <code class="prettyprint">options</code>.
</p>

<ul>
  <li>
    The <code class="prettyprint">name</code> will be the name of the payment processor to be used when accepting a payment.
  </li>
  <li>
    <span>
      The <code class="prettyprint">type</code> will be one of either
      <code class="prettyprint">direct</code>
      or <code class="prettyprint">redirect</code>.
    </span>
    <ul>
      <li>
        <code class="prettyprint">direct</code> indicates that the payment may be taken directly through the API.
        and should not require you to redirect a user to a separate off-site endpoint to input payment information.
      </li>
      <li>
        <code class="prettyprint">redirect</code>, on the other hand, indicates that your website
        will need to redirect the customer to an offsite payment processor to enter their personal and credit card information.
        Additional info on how to handle this can be found at <a href="#omnipay-accept-payment">Accept Payment</a>
        and <a href="#omnipay-complete-payment">Complete Payment</a>.
      </li>
    </ul>
  </li>
  <li>
    The <code class="prettyprint">options</code> array is an array of strings indicating which keys the payment processor
    expects. What appears here will be entirely dependent on the payment processor selected.
    At least some of these keys may be required or optional, again, depending on the payment processor.
    See <a href="#omnipay-accept-payment">Accept Payment</a> for more information and examples of
    how to provide these options.
  </li>
</ul>
EOS
          , 'examples' => array(
              'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/omnipay HTTP/1.1
EOS
              , 'response' => <<<EOS
HTTP/1.1 200 OK
[
  {
    "name": "AuthorizeNet_AIM",
    "type": "direct",
    "options": [
      "apiLoginId",
      "transactionKey",
      "testMode",
      "developerMode"
    ]
  },
  {
    "name": "Dummy",
    "type": "direct",
    "options": []
  },
  {
    "name": "PayPal_Express",
    "type": "redirect",
    "options": [
      "username",
      "password",
      "signature",
      "testMode",
      "solutionType",
      "landingPage",
      "brandName",
      "headerImageUrl"
    ]
  },
  {
    "name": "PayPal_Pro",
    "type": "direct",
    "options": [
      "username",
      "password",
      "signature",
      "testMode"
    ]
  },
  {
    "name": "SagePay_Direct",
    "type": "direct",
    "options": [
      "vendor",
      "testMode",
      "referrerId"
    ]
  },
  {
    "name": "Stripe",
    "type": "direct",
    "options": [
      "apiKey"
    ]
  }
]
EOS
            )
        );
    }

    private function acceptPayment() {
        return array(
          'type' => 'create'
          , 'info' => array(
              'verb' => 'POST'
              , 'verb_icon' => 'export'
              , 'subroute' => '/purchase'
          )
          , 'id' => 'accept-payment'
          , 'header' => 'Accept Payment'
          , 'header_icon' => 'plus'
          , 'usage' => <<<EOS
<p>
  The Accept Payment call can be used to accept a payment
  using a provided third party processor.
</p>
<p>
  <strong>Please note</strong> that this is simply a method of accepting payments through
  your third party payment processor. This does <em>not</em> insert a payment record
  into ClubSpeed, or update a check in any way. To indicate payments or check changes in ClubSpeed,
  you should use the corresponding API calls directly.
</p>
<p>
  The request body accepts the following items:
  <code class="prettyprint">name</code>,
  <code class="prettyprint">options</code>,
  <code class="prettyprint">amount</code>,
  <code class="prettyprint">transactionId</code>,
  <code class="prettyprint">currency</code>,
  <code class="prettyprint">returnUrl</code>,
  and <code class="prettyprint">card</code>.
</p>
<ul>
  <li>The <code class="prettyprint">name</code> will indicate which payment processor to use.</li>
  <li>
    The <code class="prettyprint">options</code> needs to be an object containing the key/value pairs for the payment processor
    as determined by the list of keys in <a href="#omnipay-list-vendors">List Vendors</a>
    and the values provided by your payment processor.
  </li>
  <li>The <code class="prettyprint">amount</code> is the monetary amount for which you wish to accept a payment.</li>
  <li>
    The <code class="prettyprint">transactionId</code> is a way to connect the transaction you are about to process
    with a predetermined connection. We suggest using a <code class="prettyprint">checkId</code>
    from the relevant <a href="#checks">check</a>. This property
    is not always required, depending on the payment processor.
  </li>
  <li>
    The <code class="prettyprint">currency</code> is a way to tell the processor
    which currency you are currently accepting. Most payment processors do not require or respect this setting,
    and instead require the currency to be set up in their own admin panels ahead of time.
  </li>
  <li>
    The <code class="prettyprint">returnUrl</code> is used with redirect processors
    to tell the external website to which url the customer should be redirected to
    once their payment is complete (or canceled, or errored). This should typically
    be an endpoint on your website and should include collecting response data
    and making an additional call to <a href="#omnipay-complete-payment">Complete Payment</a>.
  </li>
  <li>
    The <code class="prettyprint">card</code> is an object containing credit card information.
    This piece is required on direct processors, and may or may not be required for redirect processors.
  </li>
</ul>
<p>
  The response you receive will depend on whether or not your payment processor
  is of type <code class="prettyprint">direct</code> or <code class="prettyprint">redirect</code>.
</p>
<br>
<p>
  For direct payments, you will receive <code class="prettyprint">type</code>,
  <code class="prettyprint">code</code>,
  <code class="prettyprint">message</code>,
  <code class="prettyprint">reference</code>,
  and <code class="prettyprint">data</code>
  in the response body.
</p>
<p>
  These returns will hold information which varies from processor to processor,
  but a status returned of <code class="prettyprint">200 OK</code>
  can be considered a successful payment.
  Any status codes in 4XX-5XX should check the response body
  for an encoded <code class="prettyprint">error.message</code>.
</p>
<p>
  The <code class="prettyprint">reference</code> will typically be a unique identifier generated by the payment processor.
  We recommend storing this in the follow up ClubSpeed <a href="#payments">payment</a> creation
  as <code class="prettyprint">Payment.transactionReference</code>
</p>
<br>
<p>
  For redirect payments, you will receive <code class="prettyprint">type</code>,
  <code class="prettyprint">redirectUrl</code>,
  <code class="prettyprint">redirectMethod</code>,
  and <code class="prettyprint">redirectData</code>
  in the response body.
</p>
<p>
  Using that information, you should redirect the customer to the
  <code class="prettyprint">redirectUrl</code> using
  <code class="prettyprint">redirectMethod</code> (one of <code class="prettyprint">GET</code>,
  <code class="prettyprint">POST</code>) and supplying
  <code class="prettyprint">redirectData</code> (url encoded in the querystring for <code class="prettyprint">GET</code>,
  in the request body for <code class="prettyprint">POST</code>) to the external site.
  In this case, an additional call to <a href="#omnipay-complete-payment">Complete Payment</a>
  will need to be made through the API once the customer
  has been returned to your website after entering their information on the payment processor's website.
</p>
EOS
          , 'examples' => array(
              'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/omnipay HTTP/1.1
{
    "name": "AuthorizeNet_AIM",
    "options": {
      "apiLoginId": "supersecretlogin",
      "transactionKey": "supersecretkey",
      "testMode": true,
      "developerMode": true
    },
    "amount": 2.00,
    "transactionId": 123456,
    "card": {
        "firstName": "Bob",
        "lastName": "Bobbinson",
        "number": "4242424242424242",
        "expiryMonth": "7",
        "expiryYear": "2019",
        "startMonth": "",
        "startYear": "",
        "cvv": "162",
        "issueNumber": "",
        "address1": "123 Billing St",
        "address2": "Billpartment 1",
        "city": "Billstown",
        "postcode": "12345",
        "state": "CA",
        "country": "US",
        "phone": "(555) 123-4567",
        "email": ""
    }
}
EOS
              , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "type": "success",
  "code": "1",
  "message": "This transaction has been approved.",
  "reference": "2254355152",
  "data": [
    "1",
    "1",
    "1",
    "This transaction has been approved.",
    "HV78IK",
    "Y",
    "2254355152",
    "123456",
    "",
    "2.00",
    "CC",
    "auth_capture",
    "",
    "Bob",
    "Bobbinson",
    "",
    "123 Billing St \nBillpartment 1",
    "Billstown",
    "CA",
    "12345",
    "US",
    "(555) 123-4567",
    "",
    "",
    "Bob",
    "Bobbinson",
    "",
    "123 Billing St \nBillpartment 1",
    "Billstown",
    "CA",
    "12345",
    "US",
    "",
    "",
    "",
    "",
    "",
    "B29B6129C7E5A1B6A4104546B69AB5FC",
    "P",
    "2",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "XXXX4242",
    "Visa",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    ""
  ]
}
EOS
            )
        );
    }

    private function completePayment() {
        return array(
          'type' => 'create'
          , 'info' => array(
              'verb' => 'POST'
              , 'verb_icon' => 'export'
              , 'subroute' => '/complete'
          )
          , 'id' => 'complete-payment'
          , 'header' => 'Complete Payment'
          , 'header_icon' => 'check'
          , 'usage' => <<<EOS
<p>
  The complete payment method is only relevant for redirect payment processors,
  and should be used to pass information back to the payment processor
  once the user is returned to your site after an offsite payment attempt.
</p>
<p>
  The full application flow is as follows:
  <ol>
    <li>Initiate payment through API's /omnipay/purchase</li>
    <li>Receive redirect response from API</li>
    <li>Using your website and the API response, redirect customer to offsite payment portal</li>
    <li>Customer submits personal / credit card information into offsite payment portal</li>
    <li>Offsite payment redirects customer back to your website</li>
    <li>Your website collects response information from offsite payment portal (most likely from the query string)</li>
    <li>Encode and post this data in a request body to API's /omnipay/complete</li>
    <li>API returns response stating whether payment was successfully accepted</li>
  </ol>
</p>
<p>
  The data which should be posted to /omnipay/complete will, again,
  completely depend on the type of payment processor being used,
  and the response will take the same format as <a href="#omnipay-accept-payment">Accept Payment</a>.
</p>
EOS
        );
    }
}