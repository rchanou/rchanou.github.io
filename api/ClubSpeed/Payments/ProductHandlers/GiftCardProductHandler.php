<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;

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
        $now = \ClubSpeed\Utility\Convert::getDate();
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

            $customerId = $this->logic->customers->create((array)$giftCardCustomer); // convert back to array for the params to be handled properly with the customer interface
            Log::debug('CheckDetailID ' . $checkTotal->CheckDetailID . ': Created customer representation of the gift card with a Card ID of ' . $giftCardCustomer->CrdID);
        }
        catch(\Exception $e) {
            // note that we can't really add the giftCardHistory if we don't have this customerId -- break early (??)
            $message = 'CheckDetailID ' . $checkTotal->CheckDetailID . ': Unable to create customer record for the gift card! ' . $e->getMessage();
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
            Log::debug('CheckDetailID ' . $checkTotal->CheckDetailID . ': Created gift card history for Gift Card Customer ID ' . $giftCardHistory->CustID);
        }
        catch(\Exception $e) {
            $message = 'CheckDetailID ' . $checkTotal->CheckDetailID . ': Unable to create gift card history record!' . $e->getMessage();
            Log::error($message);
            return array(
                'error' => $message // or include more information?
            );
        }
        return array(
            'success' => 'CheckDetailID ' . $checkTotal->CheckDetailID .': Gift Card #' . $giftCardCustomer->CrdID . ' has a balance of: ' . $giftCardHistory->Points
        );
    }
}