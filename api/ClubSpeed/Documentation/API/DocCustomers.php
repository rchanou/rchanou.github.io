<?php

namespace ClubSpeed\Documentation\API;

class DocCustomers Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'customers';
        $this->header  = 'Customers';
        $this->url     = 'customers';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    The <code class="prettyprint">Customers</code> resource is a direct link to the list of customers for a location.
    Note that the term <code class="prettyprint">Customer</code> and <code class="prettyprint">Racer</code> are typically used interchangably
    throughout the ClubSpeed API, but the <code class="prettyprint">/customers</code> endpoint should be considered
    the direct window into the resource.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
  "customerId": 1002001,
  "accountCreated": "2014-04-16T00:00:00.00",
  "Address": "123 Billing Street",
  "Address2": "Billpartment 1",
  "birthdate": "2001-05-10T00:00:00.00",
  "cardId": 123456,
  "City": "Billstown",
  "company": "",
  "Country": "US",
  "creditLimit": 0,
  "creditOnHold": false,
  "Custom1": "",
  "Custom2": "",
  "Custom3": "",
  "Custom4": "1",
  "deleted": false,
  "donotemail": false,
  "email": "someone@mail.com",
  "fax": "",
  "firstname": "Real",
  "gender": 1,
  "generalNotes": ""
  "howdidyouhearaboutus": 0,
  "ignoreDOB": false,
  "isEmployee": false,
  "isGiftCard": false,
  "lastname": "Fakerson",
  "lastVisited": "2014-04-16T00:00:00.00",
  "LicenseNumber": "",
  "membershipStatus": 0,
  "membershipText": "",
  "memberShipTextLong": "",
  "mobilephone": "",
  "originalId": 0,
  "phoneNumber": "123-456-7890",
  "phoneNumber2": "",
  "priceLevel": 1,
  "privacy1": false,
  "proskill": 1200,
  "racername": "the_real_faker",
  "State": "CA",
  "status1": 1,
  "status2": 0,
  "status3": 0,
  "status4": 0,
  "totalRaces": 3,
  "totalVisits": 1,
  "waiver": 1,
  "waiver2": 7,
  "webUserName": "",
  "Zip": "12345",
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "required" => true,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "accountCreated",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The timestamp when the customer record was created"
            ),
            array(
                "name" => "Address",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The first address line for the customer"
            ),
            array(
                "name" => "Address2",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The second address line for the customer"
            ),
            // array(
            //     "name" => "award1",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "award2",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "birthdate",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The birthdate of the customer"
            ),

            array(
                "name" => "City",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The city of residence for the customer"
            ),
            array(
                "name" => "company",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The company of the customer"
            ),
            array(
                "name" => "Country",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The country of residence for the customer"
            ),
            array(
                "name" => "cardId",
                "type" => "Integer",
                "default" => "-1",
                "required" => false,
                "description" => "The membership card number for the customer"
            ),
            array(
                "name" => "creditLimit",
                "type" => "Double",
                "default" => "0",
                "required" => false,
                "description" => "The credit limit for the customer"
            ),
            array(
                "name" => "creditOnHold",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether the customer's credit is on hold"
            ),
            array(
                "name" => "Custom1",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Custom data holder 1"
            ),
            array(
                "name" => "Custom2",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Custom data holder 2"
            ),
            array(
                "name" => "Custom3",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Custom data holder 3"
            ),
            array(
                "name" => "Custom4",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Custom data holder 4"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether the customer has been soft deleted"
            ),
            array(
                "name" => "donotmail",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether the customer does not wish to receive mail"
            ),
            array(
                "name" => "email",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The email address for the customer"
            ),
            array(
                "name" => "fax",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The fax number for the customer"
            ),
            array(
                "name" => "firstname",
                "type" => "String",
                "default" => "",
                "required" => true,
                "description" => "The first name of the customer"
            ),
            array(
                "name" => "gender",
                "type" => "Integer",
                "default" => "0",
                "required" => true,
                "description" => ""
                    ."\n<span>"
                    ."\n  The gender of the customer"
                    ."\n</span>"
                    ."\n<ol start=\"0\">"
                    ."\n  <li>Other / Unspecified</li>"
                    ."\n  <li>Male</li>"
                    ."\n  <li>Female</li>"
                    ."\n</ol>"
            ),
            array(
                "name" => "generalNotes",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Any notes about the customer"
            ),
            // array(
            //     "name" => "hash",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "hotel",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "ignoreDOB",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether or not the birthdate of the customer can be ignored"
            ),
            // array(
            //     "name" => "industryId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "isEmployee",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether the customer record is also an employee"
            ),
            array(
                "name" => "isGiftCard",
                "type" => "Boolean",
                "default" => "",
                "required" => false,
                "description" => "Flag indicating whether the customer record should be considered a gift card"
            ),
            // array(
            //     "name" => "issuedBy",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "lastUnSubscribedDate",
            //     "type" => "DateTime",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "lastVisited",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the customer last visited"
            ),
            array(
                "name" => "LicenseNumber",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The license number for the customer"
            ),
            array(
                "name" => "lastname",
                "type" => "String",
                "default" => "",
                "required" => true,
                "description" => "The last name of the customer"
            ),
            array(
                "name" => "membershipStatus",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "Denormalized "
            ),
            array(
                "name" => "membershipText",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Abbreviation of the membership for the customer"
            ),
            array(
                "name" => "memberShipTextLong",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Description of the membership for the customer"
            ),
            array(
                "name" => "mobilephone",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The mobile phone for the customer"
            ),
            // array(
            //     "name" => "originalId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "password",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "phoneNumber",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The phone number for the customer"
            ),
            array(
                "name" => "phoneNumber2",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The alternate phone number for the customer"
            ),
            // array(
            //     "name" => "priceLevel",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "privacy1",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "Flag indicating whether the name of the customer should be hidden from race results"
            ),
            // array(
            //     "name" => "privacy2",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "privacy3",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "privacy4",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "promotionCode",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "racername",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The nickname for the customer"
            ),
            // array(
            //     "name" => "refId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "proskill",
                "type" => "Integer",
                "default" => "1200",
                "required" => false,
                "description" => "The current proskill for the customer"
            ),
            array(
                "name" => "howdidyouhearaboutus",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => "The ID of the <a href=\"#sources\">source</a> for the customer"
            ),
            array(
                "name" => "State",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The state of residence for the customer"
            ),
            array(
                "name" => "status1",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "Integer flag indicating a specific status. This is customizable per venue to handle indicators such as Customer added from POS, Customer added from Online Event Registration, Customer signed secondary waiver, etc. These are typically handled automatically, and should most likely be left alone while using the API"
            ),
            array(
                "name" => "status2",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "See Status1"
            ),
            array(
                "name" => "status3",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "See Status1"
            ),
            array(
                "name" => "status4",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "See Status1"
            ),
            array(
                "name" => "totalRaces",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => "Denormalized count of the total number of times this customer has raced"
            ),
            array(
                "name" => "totalVisits",
                "type" => "Integer",
                "default" => "0",
                "required" => false,
                "description" => "Denormalized count of the total number of times this customer has visited"
            ),
            array(
                "name" => "waiver",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "Integer flag indicating which primary waiver the customer has signed"
            ),
            array(
                "name" => "waiver2",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "Integer flag indicating which secondary waiver the customer has signed"
            ),
            // array(
            //     "name" => "webUserName",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "Zip",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The post code for the address of the customer"
            )
        );
    }
}
