<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Templates\TemplateService as Templates;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Utility\Convert as Convert;

class GiftCardProductHandler extends BaseProductHandler {

    public function __construct(&$logic) {
        parent::__construct($logic);
    }

    private function generateCardId() {
        // move to utility class if necessary
        // note that we can't really move it to GiftCardHistoryLogic, 
        // since the CrdID lives on the dbo.Customers record
        $cardId = -1;
        while($cardId < 0) {
            // where does the venue id come from? -- it doesn't. just use a random number.
            $tempCardId = mt_rand(1000000000, 2147483647); // get a random 10 digit number, up to the max unsigned int value
            $customer = $this->logic->customers->find("CrdID = " . $tempCardId);
            if (empty($customer))
                $cardId = $tempCardId; // card id was not being used yet, we can use this one
        }
        return $cardId;
    }

    public function handle($checkTotal, $metadata = array()) {
        $logPrefix = "Check #" . $checkTotal->CheckID . ": CheckDetail #" . $checkTotal->CheckDetailID . ": ";
        $return = array();
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
                $giftCardCustomer->BirthDate = \ClubSpeed\Utility\Convert::toDateForServer('1970-01-01 00:00:00'); // hack to satisfy customer interface logic

                $customerId = $this->logic->customers->create_v0((array)$giftCardCustomer); // convert back to array for the params to be handled properly with the old customer interface
                Log::debug($logPrefix . 'Created customer representation of gift card #' . $giftCardCustomer->CrdID);
            }
            catch(\Exception $e) {
                // note that we can't really add the giftCardHistory if we don't have this customerId -- break early (??)
                $message = $logPrefix . 'Unable to create customer record for the gift card! ' . $e->getMessage();
                Log::error($message);
                return array(
                    'error' => $message // support message?
                );
            }
            try {
                $giftCardHistory = $this->logic->giftCardHistory->dummy();
                $product = $this->logic->products->get($checkTotal->ProductID);
                $product = $product[0];
                $giftCardHistory->CustID = $customerId;
                $giftCardHistory->Points = $product->Price1; // using price1 for now, G_Points is also a possibility (what's the difference?)
                $giftCardHistory->Notes = 'Reload at Check ID ' . $checkTotal->CheckID;
                $giftCardHistory->CheckID = $checkTotal->CheckID;
                $giftCardHistory->CheckDetailID = $checkTotal->CheckDetailID;
                $giftCardHistoryId = $this->logic->giftCardHistory->create($giftCardHistory);
                Log::debug($logPrefix . 'Added ' . $giftCardHistory->Points . ' points to gift card #' . $giftCardCustomer->CrdID);
            }
            catch(\Exception $e) {
                $message = $logPrefix . 'Unable to create gift card history record for gift card #'. $giftCardCustomer->CrdID . '!' . $e->getMessage();
                Log::error($message);
                return array(
                    'error' => $message // or include more information?
                );
            }

            // TODO: fire off a gift card email containing the card number
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

                $receiptBody = "<div>Card Created! Gift card #" . $giftCardCustomer->CrdID . "</div>";

                $mail = Mail::builder()
                    ->subject($businessName . ' Gift card for Order Number: ' . $checkTotal->CheckID)
                    ->from($emailFrom)
                    ->to($emailTo)
                    ->body($receiptBody);
                Mail::send($mail);
                Log::debug($logPrefix . 'Sent gift card email to: ' . $customer->EmailAddress . ' for gift card #' . $giftCardCustomer->CrdID);
            }
            catch(\Exception $e) {
                $message = $logPrefix . 'Unable to send gift card email! ' . $e->getMessage();
                Log::error($message);
                return array(
                    'error' => $message // or include more information?
                );
            }
            // can't return now on success, if qty is greater than 1?
            return array(
                'success' => $logPrefix . ': Gift Card #' . $giftCardCustomer->CrdID . ' has a balance of: ' . $giftCardHistory->Points
            );
        }

        return $success;
    }
}