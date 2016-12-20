<?php

namespace ClubSpeed\Documentation\API;

class DocGiftCardHistory Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'gift-card-history';
        $this->header  = 'Gift Card History';
        $this->url     = 'giftCardHistory';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
        $this->readonly = true;
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
  A rolling transactional log keeping track of each change that occurs with gift cards.
  Note that in ClubSpeed, each gift card is registered as a <code class="prettyprint">Customer</code>,
  and so the gift card identifier should be considered <code class="prettyprint">customerId</code> in this dataset.
  To get the gift card number, the jump needs to be made from <code class="prettyprint">GiftCardHistory.customerId</code>
  to <code class="prettyprint">Customers.cardId</code>
</p>
<p>
  Also note that this resource is where balances and changes for a customer account can be found as well.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "giftCardHistoryId": 27611,
    "customerId": 1314321,
    "userId": 0,
    "points": 10,
    "type": 0,
    "notes": "Reload at Check ID 17346",
    "checkId": 17346,
    "checkDetailId": 35161,
    "ipAddress": "",
    "transactionDate": "2015-12-02T09:01:46.00"
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "giftCardHistoryId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "checkDetailId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The check line item which resulted in this change"
            ),
            array(
                "name" => "checkId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The check which resulted in this change"
            ),
            array(
                "name" => "customerId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The holder of the card. Note that this may correspond to either a standard gift card or member card on a customer account"
            ),
            // array(
            //     "name" => "eurekasCheckId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "eurekasDBName",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            // array(
            //     "name" => "eurekasPaidInvoice",
            //     "type" => "String",
            //     "default" => "",
            //     "required" => false,
            //     "description" => ""
            // ),
            array(
                "name" => "ipAddress",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The IP address of the machine from which the change originated"
            ),
            array(
                "name" => "notes",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "Any notes for the transaction"
            ),
            array(
                "name" => "points",
                "type" => "Double",
                "default" => "",
                "required" => false,
                "description" => "The number of changed points (which can be considered <strong>currency / money</strong>) for this transaction. Note that a summation of this field, excluding any records which have a <strong>voided</strong> type, can be considered the current balance for the card"
            ),
            array(
                "name" => "transactionDate",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the transaction occurred"
            ),
            array(
                "name" => "type",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => ""
                    ."\n<span>"
                    ."\n  The type of the transaction"
                    ."\n</span>"
                    ."\n<ol start=0>"
                    ."\n  <li value=\"0\">Sold gift card</li>"// GIFT_CARD_HISTORY_SELL_GIFT_CARD
                    ."\n  <li value=\"1\">Transferred gift card in</li>"// GIFT_CARD_HISTORY_TRANSFER_IN
                    ."\n  <li value=\"9\">Void sold gift card</li>"// GIFT_CARD_HISTORY_VOID_SELL
                    ."\n  <li value=\"10\">Paid with gift card</li>"// GIFT_CARD_HISTORY_PAY_BY_GIFT_CARD
                    ."\n  <li value=\"11\">Void paid with gift card</li>"// GIFT_CARD_HISTORY_VOID_PAY_BY_GIFT_CARD
                    ."\n  <li value=\"12\">Refunded to gift card</li>"// GIFT_CARD_HISTORY_REFUND_TO_GIFT_CARD
                    ."\n  <li value=\"13\">Sold gift card external</li>"// GIFT_CARD_HISTORY_SELL_GIFT_CARD_EXTERNAL
                    ."\n  <li value=\"14\">Void sold gift card external</li>"// GIFT_CARD_HISTORY_VOID_SELL_EXTERNAL
                    ."\n  <li value=\"15\">Paid with gift card external</li>"// GIFT_CARD_HISTORY_PAY_BY_GIFT_CARD_EXTERNAL
                    ."\n  <li value=\"16\">Void paid with gift card external</li>"// GIFT_CARD_HISTORY_VOID_PAY_BY_GIFT_CARD_EXTERNAL
                    ."\n  <li value=\"17\">Refunded to gift card external</li>"// GIFT_CARD_HISTORY_REFUND_TO_GIFT_CARD_EXTERNAL
                    ."\n  <li value=\"18\">Invoice paid</li>"// GIFT_CARD_HISTORY_INVOICE_PAID
                    ."\n</ol>"
            ),
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The id of the <a href=\"#users\">user</a> which recorded the transaction"
            )
        );
    }
}
