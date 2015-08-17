<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Utility\Convert as Convert;

class PointProductHandler extends BaseProductHandler {

    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
    }

    public function handle($checkTotal, $metadata = array()) {
        $logPrefix = "Check #" . $checkTotal->CheckID . ": CheckDetail #" . $checkTotal->CheckDetailID . ": ";
        $now = Convert::getDate();
        $pointHistory = $this->logic->pointHistory->dummy();
        if ($checkTotal->P_Points != 0) {
            try {
                $pointHistory->CheckDetailID     = $checkTotal->CheckDetailID;
                $pointHistory->CheckID           = $checkTotal->CheckID;
                $pointHistory->CustID            = $checkTotal->CustID;
                $pointHistory->Notes             = 'CheckID ' . $checkTotal->CheckID;
                $pointHistory->PointAmount       = $checkTotal->P_Points * $checkTotal->Qty; // default to 1?
                $pointHistory->PointDate         = $now;
                $pointHistory->PointExpDate      = Convert::toDateForServer('2038-01-18');
                $pointHistory->ReferenceID       = 0; // used when transferring points from one customer to another
                $pointHistory->RefPointHistoryID = 0; // required by front end
                $pointHistory->ReservationID     = 0;
                $pointHistory->Type              = Enums::POINT_HISTORY_BUY;
                $pointHistory->UserID            = 1;
                $pointHistory->Username          = 'api';
                $this->logic->pointHistory->create($pointHistory);
                $message = $logPrefix . 'Modified points for Customer #' . $pointHistory->CustID . ' by ' . $pointHistory->PointAmount;
                Log::info($message, Enums::NSP_BOOKING);
            }
            catch (\Exception $e) {
                $message = $logPrefix . 'Unable to add points to Customer #' . $checkTotal->CustID . "! " . $e->getMessage();
                Log::error($message, Enums::NSP_BOOKING);
                throw new \Exception($message);
            }
        }
        $customers = $this->logic->customers->get($checkTotal->CustID);
        $customer = $customers[0];

        return 'Assigned ' . $pointHistory->PointAmount . ' to ' . $customer->FName . ' ' . $customer->LName;
        // return array('success' => ''); // any messages to show on Receipt?
    }
}