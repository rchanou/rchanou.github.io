<?php

namespace ClubSpeed\Documentation\API;

class DocCustomers Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'customers';
        $this->header          = 'Customers';
        $this->url             = 'customers';
        $this->info            = $this->info();
        $this->version         = 'V2';
        $this->json            = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "customerId": 1002001,
  "company": "",
  "firstname": "Real",
  "lastname": "Fakerson",
  "racername": "the_real_faker",
  "birthdate": "2001-05-10T00:00:00.00",
  "ignoreDOB": false,
  "gender": 1,
  "email": "someone@mail.com",
  "howdidyouhearaboutus": 0,
  "hotel": "",
  "industryId": 0,
  "refId": 0,
  "donotemail": false,
  "Address": "123 Billing Street",
  "Address2": "Billpartment 1",
  "City": "Billstown",
  "State": "CA",
  "Zip": "12345",
  "Country": "US",
  "phoneNumber": "123-456-7890",
  "phoneNumber2": "",
  "mobilephone": "",
  "fax": "",
  "LicenseNumber": "",
  "issuedBy": "",
  "waiver": 1,
  "waiver2": 7,
  "cardId": 123456,
  "proskill": 1200,
  "accountCreated": "2014-04-16T00:00:00.00",
  "lastVisited": "2014-04-16T00:00:00.00",
  "totalVisits": 1,
  "totalRaces": 3,
  "membershipStatus": 0,
  "membershipText": "",
  "memberShipTextLong": "",
  "priceLevel": 1,
  "promotionCode": "",
  "isGiftCard": false,
  "webUserName": "",
  "award1": 0,
  "award2": 0,
  "Custom1": "",
  "Custom2": "",
  "Custom3": "",
  "Custom4": "1",
  "privacy1": false,
  "privacy2": false,
  "privacy3": false,
  "privacy4": false,
  "status1": 1,
  "status2": 0,
  "status3": 0,
  "status4": 0,
  "deleted": false,
  "isEmployee": false,
  "originalId": 0,
  "creditLimit": 0,
  "creditOnHold": false,
  "generalNotes": ""
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "accountCreated",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Address",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Address2",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "award1",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "award2",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "birthdate",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "mobilephone",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "City",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "company",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Country",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "cardId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "creditLimit",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "creditOnHold",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Custom1",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Custom2",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Custom3",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Custom4",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "donotmail",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "email",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "fax",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "firstname",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "gender",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "generalNotes",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "hash",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "hotel",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "ignoreDOB",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "industryId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "isEmployee",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "isGiftCard",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "issuedBy",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "lastUnSubscribedDate",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "lastVisited",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "LicenseNumber",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "lastname",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "membershipStatus",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "membershipText",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "memberShipTextLong",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "originalId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
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
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "phoneNumber2",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "priceLevel",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "privacy1",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "privacy2",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "privacy3",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "privacy4",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "promotionCode",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "racername",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "refId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "proskill",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "howdidyouhearaboutus",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID of the Source for the Customer"
            ),
            array(
                "name" => "State",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "status1",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "status2",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "status3",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "status4",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "totalRaces",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "totalVisits",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "waiver",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "waiver2",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "webUserName",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "Zip",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            )
        );
    }
}
