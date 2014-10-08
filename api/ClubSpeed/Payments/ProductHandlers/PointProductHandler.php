<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;

class PointProductHandler extends BaseProductHandler {

    public function __construct(&$logic) {
        parent::__construct($logic);
    }

    public function handle($checkTotal, $metadata = array()) {
        $now = \ClubSpeed\Utility\Convert::getDate();
        if ($checkTotal->P_Points != 0) {
            try {
                $pointHistory = $this->logic->pointHistory->dummy();
                $pointHistory->CustID = $checkTotal->CustID;
                $pointHistory->CheckID = $checkTotal->CheckID;
                $pointHistory->UserID = 1;
                $pointHistory->ReferenceID = 0; // used when transferring points from one customer to another
                $pointHistory->Type = 0;
                $pointHistory->PointDate = $now;
                $pointHistory->PointExpDate = \ClubSpeed\Utility\Convert::toDateForServer('2999-12-31');
                $pointHistory->Notes = 'CheckID ' . $checkTotal->CheckID;
                $pointHistory->CheckDetailID = $checkTotal->CheckDetailID;
                $pointHistory->PointAmount = $checkTotal->P_Points;
                $pointHistory->Username = 'api';
                $this->logic->pointHistory->create($pointHistory);
                Log::debug('CheckDetailID ' . $checkTotal->CheckDetailID . ': Added ' . $pointHistory->PointAmount . ' points to Customer ' . $pointHistory->CustID);
            }
            catch (\Exception $e) {
                Log::error('CheckDetailID ' . $checkTotal->CheckDetailID . ': Unable to add points to Customer ' . $checkTotal->CustID, $e);
            }
        }

        if (!empty($metadata)) {
            if (isset($metadata['heatId'])) {
                // if a heatId is passed along with the metadata, assume the customer also needs to be added to the race
                // and have their points reduced by a single race amount

                // attempt to reduce the customers points
                try {
                    $heatId                               = \ClubSpeed\Utility\Convert::toNumber($metadata['heatId']);
                    $customer                             = $this->logic->customers->get($checkTotal->CustID);
                    $customer                             = $customer[0];
                    $pointHistoryReduction                = $this->logic->pointHistory->dummy();
                    $pointHistoryReduction->CustID        = $checkTotal->CustID;
                    $pointHistoryReduction->CheckID       = Enums::DB_NULL;
                    $pointHistoryReduction->UserID        = 1;
                    $pointHistoryReduction->ReferenceID   = 0;
                    $pointHistoryReduction->Type          = 3; // PointHistoryType for reduced heat (this may be deprecated, including for safety's sake)
                    $pointHistoryReduction->PointDate     = $now;
                    $pointHistoryReduction->PointExpDate  = \ClubSpeed\Utility\Convert::toDateForServer('2999-12-31');
                    $pointHistoryReduction->Notes         = 'HeatNo ' . $heatId;
                    $pointHistoryReduction->CheckDetailID = Enums::DB_NULL;
                    $pointHistoryReduction->PointAmount   = -1 * ($checkTotal->P_Points / $checkTotal->Qty);
                    $pointHistoryReduction->Username      = 'api';
                    $pointHistoryReductionId              = $this->logic->pointHistory->create($pointHistoryReduction);
                    $pointHistoryReductionId              = $pointHistoryReductionId['PointHistoryID'];
                    Log::debug('CheckDetailID ' . $checkTotal->CheckDetailID . ': Took ' . $pointHistoryReduction->PointAmount . ' points from Customer ' . $pointHistoryReduction->CustID);
                }
                catch (\Exception $e) {
                    Log::error('CheckDetailID ' . $checkTotal->CheckDetailID . ': Unable to remove ' . $pointHistoryReduction->PointAmount . ' points from Customer ' . $checkTotal->CustID, $e);
                }

                // attempt to add the customer to the heat
                // note that sql will throw 500 exception if the HeatNo/CustID combination already exists
                try {
                    $heatDetails                 = $this->logic->heatDetails->dummy();
                    $heatDetails->HeatNo         = $heatId;
                    $heatDetails->CustID         = $checkTotal->CustID;
                    $heatDetails->RPM            = $customer->RPM;
                    $heatDetails->PointHistoryID = $pointHistoryReducedId;
                    $this->logic->heatDetails->create($heatDetails);
                    Log::debug('CheckDetailID ' . $checkTotal->CheckDetailID . ': Added ' . $customer->FName . ' ' . $customer->LName . ' to race #' . $heatId);
                }
                catch(\Exception $e) {
                    Log::error('CheckDetailID ' . $checkTotal->CheckDetailID . ': Unable to add' . $customer->FName . ' ' . $customer->LName . ' to race #' . $heatId, $e);
                }

                // attempt to add any additional reservations by incrementing the heat's NumberOfReservation
                try {
                    $additionalReservations = \ClubSpeed\Utility\Convert::toNumber($metadata['additionalReservations']);
                    if (!empty($additionalReservations)) {
                        $heat                      = $this->logic->heatMain->get($heatId);
                        $heat                      = $heat[0];
                        $heat->NumberOfReservation = ($heat->NumberOfReservation ?: 0) + $additionalReservations;
                        $this->logic->heatMain->update($heat->HeatNo, $heat);
                        Log::debug('CheckDetailID ' . $checkTotal->CheckDetailID . ': Added ' . $additionalReservations . ' additional reservations to race #' . $heatId);
                    }
                }
                catch(\Exception $e) {
                    Log::error('CheckDetailID ' . $checkTotal->CheckDetailID . ': Unable to add ' . $additionalReservations . ' additional reservations to race #' . $heatId);
                }
            }
        }
    }
}