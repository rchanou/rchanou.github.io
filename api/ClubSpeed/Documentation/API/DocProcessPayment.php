<?php

namespace ClubSpeed\Documentation\API;

class DocProcessPayment Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id     = 'process-payment';
        $this->header = 'Process Payment';
        $this->url    = 'processPayment';
        $this->root = '/api/index.php/processPayment';
        $this->info = $this->info();
        $this->expand();
        $this->calls['process'] = $this->process();
        // $this->calls['login'] = $this->login();
        // $this->calls['fb-login'] = $this->fbLogin();
    }

    private function info() {
        $info = <<<EOS
[
    {
        "name": "name",
        "type": "String",
        "required": [
            "process",
            "create"
        ],
        "available": [],
        "unavailable": [],
        "description": "The name of the Omnipay payment processor."
    },
    {
        "name": "options",
        "type": "Object",
        "required": [
            "process",
            "create"
        ],
        "available": [],
        "unavailable": [],
        "description": "The object containing Omnipay payment processor options. Note that these properties will vary based on the selected payment processor."
    },
    {
        "name": "check",
        "type" : "Object",
        "required": [
            "process"
        ],
        "description": "The object containing Check properties."
    },
    {
        "name": "check.checkId",
        "type" : "Integer",
        "required": [
            "process"
        ],
        "description": "The ID for the Check to be paid."
    },
    {
        "name": "check.details",
        "type" : "Array<Detail>",
        "available": [
            "process"
        ],
        "description": "The array of details objects to be processed at the same time as the Check Payment is completed."
    },
    {
        "name": "check.checkId",
        "type" : "Integer",
        "required": [
            "process"
        ],
        "description": "The ID for the Check to be paid."
    },
    {
        "name": "card",
        "type" : "Object",
        "description": "The credit card object."
    },
    {
        "name": "card.firstName",
        "type" : "String",
        "description": "The first name on the credit card."
    },
    {
        "name": "card.lastName",
        "type" : "String",
        "description": "The last name on the credit card."
    },
    {
        "name": "card.number",
        "type" : "Integer",
        "description": "The credit card number."
    },
    {
        "name": "card.expiryMonth",
        "type" : "Integer",
        "description": "The expiration month for the credit card. Can be 1 or 2-digit."
    },
    {
        "name": "card.expiryYear",
        "type" : "Integer",
        "description": "The card's 4-digit expiration year."
    },
    {
        "name": "card.cvv",
        "type" : "Integer",
        "description": "The cvv for the credit card."
    },
    {
        "name": "card.address1",
        "type" : "Integer",
        "description": "The first address line for the credit card."
    },
    {
        "name": "card.address2",
        "type" : "Integer",
        "description": "The second address line for the credit card."
    },
    {
        "name": "card.city",
        "type" : "Integer",
        "description": "The city for the credit card."
    },
    {
        "name": "card.postcode",
        "type" : "Integer",
        "description": "The post/zip code for the credit card."
    },
    {
        "name": "card.state",
        "type" : "String",
        "description": "The state code for the credit card."
    },
    {
        "name": "card.country",
        "type" : "String",
        "description": "The country code for the credit card."
    },
    {
        "name": "card.phone",
        "type" : "String",
        "description": "The phone number for the credit card."
    }
]
EOS;
        return json_decode($info, true);
    }

    private function process() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root,
                'verb' => 'POST',
                'verb_icon' => 'export',
                'required' => array(
                    'name',
                    'options',
                    'check',
                    'check.checkId'
                ),
                'available' => array(
                    'card'
                ),
                'unavailable' => array(
                )
            ),
            'usage' => <<<EOS
<p>
    Executing this method will attempt to pay for all items on the provided <code class="prettyprint">checkId</code>
    using the provided credit card.
</p>
<p>
    This payment processor will accept a number of processors based on the <a href="https://github.com/thephpleague/omnipay">Omnipay</a> library.
    Each of these processors will require a different set of options, usually including a set of credentials, keys, or tokens.
</p>
<p>
    If a reservation product is being purchased (Online Booking, for example),
    then additional information will be needed in the <code class="prettyprint">check.details</code> array.
</p>
<p>
    For example, if a user has purchased a reservation product, then the API needs to receive an object
    containing the <code class="prettyprint">checkDetailId</code> for the check line item containing the reservation products,
    as well as the <code class="prettyprint">heatId</code> which was originally selected by the customer from the
    <a href="#booking-availability-list">Booking Availability</a> list.
</p>
<p>
    If the additional booking processing object is provided,
    the purchaser will be automatically added to the race where possible,
    and extra reservations will be made if multiple quantities were purchased.
</p>
<p>
    See the example below for a representation of what
    the additional processing objects should look like
    in the case of a race purchase for a specific heat.
</p>
EOS
,
            'header' => 'Process',
            'header_icon' => 'export',
            'id' => 'process',
            'type' => 'create',
            'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/processPayment HTTP/1.1
{
  "name": "SagePay_Direct",
  "options": {
    "vendor": "my_sagepay_vendor_name",
    "simulatorMode": true
  },
  "check": {
    "checkId": 2439,
    "details": [
      {
        "checkDetailId": 7763,
        "heatId": 13,
      }
    ]
  },
  "card": {
    "firstName": "Jim",
    "lastName": "Bob",
    "number": "4111111111111111",
    "expiryMonth": "7",
    "expiryYear": "2015",
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
EOS
            )
        );
    }

    private function login() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root . '/login',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'required' => array(
                    'username',
                    'password'
                ),
                'available' => array(
                ),
                'unavailable' => array(
                )
            ),
            'header' => 'Login',
            'header_icon' => 'export',
            'id' => 'login',
            'type' => 'update',
            'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/racers/login HTTP/1.1
{
    "username": "bob@clubspeed.com",
    "password": "bobssupersecretpassword"
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "customerId": 1000001
}
EOS
            )
        );
    }

    private function fbLogin() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root . '/fb_login',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'required' => array(
                    'email',
                    'facebookId',
                    'facebookToken',
                    'facebookAllowEmail',
                    'facebookAllowPost',
                    'facebookEnabled'
                ),
                'available' => array(
                    'facebookExpiresIn',
                    'racername',
                    'email',
                    'password',
                    'donotemail',
                    'firstname',
                    'lastname',
                    'birthdate',
                    'gender',
                    'mobilephone',
                    'Address',
                    'Address2',
                    'City',
                    'Country',
                    'howdidyouhearaboutus',
                    'State',
                    'Zip'
                ),
                'unavailable' => array(
                )
            ),
            'header' => 'Facebook Login',
            'header_icon' => 'export',
            'id' => 'fb-login',
            'type' => 'update',
            'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/racers/fb_login HTTP/1.1
{
  "email": "bob@clubspeed.com",
  "facebookId": "652712592679",
  "facebookToken":"AVNAWIVYANIWVUDBAWKUGDVBAWIDVYNLAWDVHNAWILDVHUNAWIULDVHNLAWIDVHUNAWUILDVHNAWILDVHN",
  "facebookExpiresIn": 9999,
  "facebookAllowEmail": true,
  "facebookAllowPost": true,
  "facebookEnabled": true
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "customerId": 1000001
}
EOS
            ),
            'usage' => <<<EOS
<p>
    Note that Facebook Login has the capability to upsert a customer record, if one does not exist.
    If this call is intended to be used to create customer records where they don't exist,
    then parameters taken from the <a href="#racers-create">Customer Create</a> call should be included as parameters here.
</p>
EOS
        );
    }
}