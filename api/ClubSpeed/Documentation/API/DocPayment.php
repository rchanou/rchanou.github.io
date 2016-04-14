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
  "customerId": 1,
  "extCardType": "Dummy",
  "payAmount": 21.24,
  "payDate": "2014-12-05T11:46:28.00",
  "payStatus": 2,
  "payTax": 1.24,
  "payTerminal": "api",
  "payType": 3,
  "transaction": null,
  "userId": 1,
  "voidDate": "2015-07-02T15:39:52.00",
  "voidNotes": "",
  "voidTerminal": "",
  "voidUser": 1
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "paymentId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => true,
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
                "required" => true,
                "description" => "The ID of the <a href=\"#checks\">check</a> for which the payment was applied"
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
                "required" => true,
                "description" => "The ID of the <a href=\"#customers\">customer</a> that has made the payment"
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
                "required" => false,
                "description" => "The card or payment processor type for the payment, where applicable"
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
                "required" => true,
                "description" => "The monetary amount of the payment"
            ),
            array(
                "name" => "payDate",
                "type" => "DateTime",
                "default" => "{Now}",
                "required" => false,
                "description" => "The timestamp at which the payment was collected"
            ),
            array(
                "name" => "payStatus",
                "type" => "Integer",
                "default" => "1",
                "required" => false,
                'description' => ''
                    ."\n<span>"
                    ."\n  The status of the payment"
                    ."\n</span>"
                    ."\n<ol>"
                    ."\n  <li>Paid</li>"
                    ."\n  <li>Void</li>"
                    ."\n</ol>"
            ),
            array(
                "name" => "payTax",
                "type" => "Double",
                "default" => "{Calculated}",
                "required" => false,
                "description" => "The monetary amount of the payment which was applied to tax. Note that <code class=\"prettyprint\">payAmount</code> is inclusive of this value"
            ),
            array(
                "name" => "payTerminal",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The terminal at which the payment was collected"
            ),
            array(
                "name" => "payType",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                'description' => ''
                  ."\n<p>"
                  ."\n  The type of the payment which was collected"
                  ."\n</p>"
                  ."\n<ol>"
                  ."\n  <li value=\"1\">Cash</li>"
                  ."\n  <li value=\"2\">Credit</li>"
                  ."\n  <li value=\"3\">External / Third Party Processor</li>"
                  ."\n  <li value=\"4\">Gift Card</li>"
                  ."\n  <li value=\"5\">Voucher</li>"
                  ."\n  <li value=\"6\">Complementary</li>"
                  ."\n</ol>"
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
                "name" => "transaction",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "A reference to a transaction number, typically one which was returned from a third party processor"
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
                "required" => false,
                "description" => "The id of the user that created the payment"
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
                "description" => "The timestamp at which the payment was voided"
            ),
            array(
                "name" => "voidNotes",
                "type" => "String",
                "default" => "",
                "description" => "The notes as to why the payment was voided"
            ),
            array(
                "name" => "voidTerminal",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The terminal where the payment was voided"
            ),
            array(
                "name" => "voidUser",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The id of the user that voided the payment"
            )
        );
    }
}
