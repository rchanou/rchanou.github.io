<?php

namespace ClubSpeed\Payments\ProductHandlers;

require_once(__DIR__.'/../../../vendors/barcode/DNS1D.php');

use ClubSpeed\Enums\Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Templates\TemplateService as Templates;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Utility\Currency;
use Dinesh\BarcodeAll\DNS1D;


class GiftCardProductHandler extends BaseProductHandler {

    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
    }

    private function generateCardId() {
        // move to utility class if necessary
        // note that we can't really move it to GiftCardHistoryLogic, 
        // since the CrdID lives on the dbo.Customers record
        $minCardId = @$GLOBALS['minCardId'] ?: 1000000000;
        $maxCardId = @$GLOBALS['maxCardId'] ?: 2147483647;
        $cardId = -1;
        while($cardId < 0) {
            $tempCardId = mt_rand($minCardId, $maxCardId);
            $customer = $this->logic->customers->find("CrdID = " . $tempCardId);
            if (empty($customer))
                $cardId = $tempCardId; // card id was not being used yet, we can use this one
        }
        return $cardId;
    }

    public function handle($checkTotal, $metadata = array()) {
        $logPrefix = "Check #" . $checkTotal->CheckID . ": CheckDetail #" . $checkTotal->CheckDetailID . ": ";
        $return = array(
            'success' => array()
        );
        for($i = 0; $i < $checkTotal->Qty; $i++) {
            // note: since the handler is now handling looping qty,
            // where should the loop end? on first error, or continue processing?
            // what does the return look like with one success, one error?
            $now = Convert::getDate();
            try {
                // generate dbo.Customers.CrdID in here
                $giftCardCustomer = $this->logic->customers->dummy();
                $giftCardCustomer->CrdID =  $this->generateCardId();
                $giftCardCustomer->FName = 'Gift Card';
                $giftCardCustomer->LName = $giftCardCustomer->CrdID; // expected to be the same as CrdID
                $giftCardCustomer->IsGiftCard = true;
                $giftCardCustomer->EmailAddress = 'giftcard' . $giftCardCustomer->CrdID . '@clubspeed.com'; // hack to satisfy customer interface logic
                $giftCardCustomer->Gender = 0; // hack to satisfy customer interface logic

                $giftCardCustomerId = $this->logic->customers->create_v0((array)$giftCardCustomer); // convert back to array for the params to be handled properly with the old customer interface
                $return['success'][] = '#' . $giftCardCustomer->CrdID; // use # to prevent tel: interpretation in html?
                Log::info($logPrefix . 'Created customer representation of gift card #' . $giftCardCustomer->CrdID, Enums::NSP_BOOKING);
            }
            catch(\Exception $e) {
                // note that we can't really add the giftCardHistory if we don't have this customerId -- break early (??)
                $message = $logPrefix . 'Unable to create customer record for the gift card! ' . $e->getMessage();
                Log::error($message, Enums::NSP_BOOKING);
                throw new \Exception($message);
            }

            try {
                $this->logic->checkDetails->update($checkTotal->CheckDetailID, array(
                    'G_CustID' => $giftCardCustomerId
                ));
            }
            catch(\Exception $e) {
                $message = $logPrefix . 'Unable to update check details to point to the gift card customer! ' . $e->getMessage();
                Log::error($message, Enums::NSP_BOOKING);
                throw new \Exception($message);
            }

            try {
                $giftCardHistory = $this->logic->giftCardHistory->dummy();
                $product = $this->logic->products->get($checkTotal->ProductID);
                $product = $product[0];
                $giftCardHistory->CustID = $giftCardCustomerId;
                $giftCardHistory->Points = $checkTotal->G_Points; // use G_Points for consistency, also use the piece on the check itself, not the product in case it has been manually manipulated.
                $giftCardHistory->Notes = 'Reload at Check ID ' . $checkTotal->CheckID;
                $giftCardHistory->CheckID = $checkTotal->CheckID;
                $giftCardHistory->CheckDetailID = $checkTotal->CheckDetailID;
                $giftCardHistoryId = $this->logic->giftCardHistory->create($giftCardHistory);
                Log::info($logPrefix . 'Added ' . $giftCardHistory->Points . ' points to gift card #' . $giftCardCustomer->CrdID, Enums::NSP_BOOKING);
            }
            catch(\Exception $e) {
                $message = $logPrefix . 'Unable to create gift card history record for gift card #'. $giftCardCustomer->CrdID . '!' . $e->getMessage();
                Log::error($message, Enums::NSP_BOOKING);
                throw new \Exception($message);
            }

            try {
                $businessName = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = BusinessName");
                $businessName = $businessName[0];
                $businessName = $businessName->SettingValue;

                $customer = $this->logic->customers->get($checkTotal->CustID);
                $customer = $customer[0];
                $emailTo  = array($customer->EmailAddress => $customer->FName . ' ' . $customer->LName);
                
                $customer = $this->logic->customers->get($checkTotal->CustID);
                $customer = $customer[0];
                $emailTo  = array($customer->EmailAddress => $customer->FName . ' ' . $customer->LName);

                $emailFrom = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = EmailWelcomeFrom");
                $emailFrom = $emailFrom[0];
                $emailFrom = array($emailFrom->SettingValue => $businessName);

                $giftCardBalance = $this->logic->giftCardBalance->match(array(
                    'CrdID' => $giftCardCustomer->CrdID // not using `get` in case primary key changes
                ));
                $giftCardBalance = $giftCardBalance[0];

                $barCodeUtil = new DNS1D(); //Bar-code generating library

                $giftCardEmailBodyHtml = $this->logic->settings->match(array(
                    'Namespace' => 'Booking',
                    'Name' => 'giftCardEmailBodyHtml'
                ));
                $giftCardEmailBodyHtml = $giftCardEmailBodyHtml[0];
                $giftCardEmailBodyHtml = $giftCardEmailBodyHtml->Value;

                $giftCardEmailSubject = $this->logic->settings->match(array(
                    'Namespace' => 'Booking',
                    'Name' => 'giftCardEmailSubject'
                ));

                $giftCardEmailSubject = $giftCardEmailSubject[0];
                $giftCardEmailSubject = $giftCardEmailSubject->Value;
                $giftCardEmailSubject = str_replace(
                    array('{{business}}', '{{checkId}}'),
                    array($businessName, $checkTotal->CheckID),
                    $giftCardEmailSubject
                );

                $giftCardEmailBodyHtml = str_replace(
                    array('{{giftCardImage}}'),
                    array('##giftCardImage##'),
                    $giftCardEmailBodyHtml
                );

                $giftCardData = array(
                    'customer'    => $customer->FName . ' ' . $customer->LName,
                    'giftCardNo'  => $giftCardCustomer->CrdID,
                    'description' => $product->Description,
                    'balance'     => Currency::toCurrencyString($giftCardBalance->Money),
                    'business'    => $businessName
                );

                $dateFormat = $this->findStringBetween($giftCardEmailBodyHtml,'{{date:','}}');

                if ($dateFormat !== null)
                {
                    $dateTag = '{{date:' . $dateFormat . '}}';
                    $date = date($dateFormat);
                    $giftCardEmailBodyHtml = str_replace(
                        array($dateTag),
                        array($date),
                        $giftCardEmailBodyHtml
                    );
                }

                $receiptBody = Templates::buildFromString($giftCardEmailBodyHtml, $giftCardData);

                $sendReceiptCopyTo = $this->logic->controlPanel->match(array(
                    'TerminalName' => 'Booking',
                    'SettingName' => 'sendReceiptCopyTo'
                ));
                if (!empty($sendReceiptCopyTo)) {
                    $sendReceiptCopyTo = $sendReceiptCopyTo[0];
                    $sendReceiptCopyTo = $sendReceiptCopyTo->SettingValue; // this will default to an empty string
                    if (!empty($sendReceiptCopyTo)) {
                        $sendReceiptCopyTo = explode(",", $sendReceiptCopyTo); // we should be able to use this directly as the BCC
                        array_walk($sendReceiptCopyTo, function(&$val) {
                            $val = trim($val); // get rid of any additional whitespace in the comma-delimited list of emails
                        });
                    }
                }

                $mail = Mail::builder()
                    ->subject($giftCardEmailSubject)
                    ->from($emailFrom)
                    ->to($emailTo)
                    ->body($receiptBody);

                if (!empty($sendReceiptCopyTo)) {
                    Log::info($logPrefix . 'Gift card receipt copies will be sent to the following addresses: ' . print_r($sendReceiptCopyTo, true), Enums::NSP_BOOKING);
                    $mail->bcc($sendReceiptCopyTo);
                }

                Mail::sendWithInlineImages($mail, array('giftCardImage' => $barCodeUtil->getBarcodePNG($giftCardCustomer->CrdID, "C128",2,60)));
                Log::info($logPrefix . 'Sent gift card email to: ' . $customer->EmailAddress . ' for gift card #' . $giftCardCustomer->CrdID, Enums::NSP_BOOKING);
            }
            catch(\Exception $e) {
                $message = $logPrefix . 'Unable to send gift card email! ' . $e->getMessage();
                Log::error($message, Enums::NSP_BOOKING);
                throw new \Exception($message);
            }
        }

        // update check notes to include all gift cards?

        $return['success'] = implode(', ', $return['success']);
        return $return['success'];
    }

    private function findStringBetween($string, $start, $end)
    {
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return null;
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }
}