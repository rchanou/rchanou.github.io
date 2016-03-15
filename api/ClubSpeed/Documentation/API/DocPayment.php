<?php

namespace ClubSpeed\Documentation\API;

class DocPayment Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'payments';
        $this->header          = 'Payments';
        $this->url             = 'payments';
        $this->info            = $this->info();
        $this->version         = 'V2';
        $this->json            = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "paymentId": 3391,
  "checkId": 3467,
  "userId": 1,
  "payTerminal": "api",
  "payType": 3,
  "payDate": "2014-12-05T11:46:28.00",
  "payStatus": 2,
  "payAmount": 21.24,
  "payTax": 1.24,
  "voidDate": "2015-07-02T15:39:52.00",
  "voidUser": 1,
  "voidTerminal": "",
  "voidNotes": "",
  "customerId": 1,
  "voucherId": null,
  "voucherNotes": null,
  "historyId": null,
  "invoicePaidHistoryId": 0,
  "extCardType": "Dummy",
  "tender": 0,
  "transaction": null
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "paymentId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            // array(
            //     "name" => "accountName",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "amount",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "autAmount",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "authorizationCode",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "avs",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "balanceRemaing",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "cardType",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "checkId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "checkingAccountName",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "checkNumber",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "creditCardNo",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "expirationDate",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "extCardType",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "externalAccountName",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "externalAccountNumber",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "historyId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "invoicePaidHistoryId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "lastFour",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "payAmount",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "payDate",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "payStatus",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "payTax",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "payTerminal",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "payType",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "referenceNumber",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "responseTime",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "resultCode",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
                // "name" => "shift",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "tender",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "transactionDate",
            //     "type" => "DateTime",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "transactionId",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "transactionReference",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "troutD",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            // array(
            //     "name" => "vid",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "voidDate",
                "type" => "DateTime",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "voidNotes",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "voidTerminal",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "voidUser",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "voucherId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            ),
            array(
                "name" => "voucherNotes",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => ""
            )
        );
    }
}
