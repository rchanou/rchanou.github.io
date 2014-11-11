<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Utility\Convert as Convert;

class PointProductHandler extends BaseProductHandler {

    public function __construct(&$logic) {
        parent::__construct($logic);
    }

    public function handle($checkTotal, $metadata = array()) {
        $logPrefix = "Check #" . $checkTotal->CheckID . ": CheckDetail #" . $checkTotal->CheckDetailID . ": ";
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
                $pointHistory->PointExpDate = Convert::toDateForServer('2038-01-18');
                $pointHistory->Notes = 'CheckID ' . $checkTotal->CheckID;
                $pointHistory->CheckDetailID = $checkTotal->CheckDetailID;
                $pointHistory->PointAmount = $checkTotal->P_Points;
                $pointHistory->Username = 'api';
                $this->logic->pointHistory->create($pointHistory);
                $message = $logPrefix . 'Added ' . $pointHistory->PointAmount . ' points to Customer ' . $pointHistory->CustID;
                Log::debug($message);
            }
            catch (\Exception $e) {
                $message = $logPrefix . 'Unable to add points to Customer ' . $checkTotal->CustID . "! " . $e->getMessage();
                Log::error($message);
                return array('error' => $message);
            }
        }

        if (empty($metadata)) {
            // then we are done processing, return the message
            return array('success' => $message); // note that message was set up above while processing normal logic
        }
        else {
            if (isset($metadata['heatId'])) {
                $heatId = null;
                $checkNotes = '';
                $heatNotes = '';

                // check for heat existence
                try {
                    $heatId     = Convert::toNumber($metadata['heatId']);
                    $heatMain   = $this->logic->heatMain->get($heatId);
                    $heatMain   = $heatMain[0];
                    $checkNotes = $checkTotal->Qty . ' reservations for Heat #' . $heatId . ' at ' . $heatMain->ScheduledTime . ' on Track #' . $heatMain->TrackNo;
                    $message    = $logPrefix . 'Heat #' . $heatId . ' passed with check details metadata';
                    Log::debug($message);
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to find Heat #' . $heatId . '! ' . $e->getMessage();
                    Log::error($message);
                    return array('error' => $message);
                }

                // update the check notes for business sanity check
                try {
                    $check = $this->logic->checks->get($checkTotal->CheckID);
                    $check = $check[0];
                    $checkNotes = (empty($check->Notes) ? $checkNotes : $check->Notes . ' :: ' . $checkNotes);
                    $this->logic->checks->update($checkTotal->CheckID, array(
                        'Notes' => $checkNotes
                    ));
                    $message = $logPrefix . 'Updated check notes to: ' . $checkNotes;
                    Log::debug($message);
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to update check notes to: ' . $checkNotes . '! Exception: ' . $e->getMessage();
                    Log::error($message);
                    return array('error' => $message);
                }

                // update the heat notes for business sanity check
                try {
                    $heatNotes = $checkTotal->Qty . ' reservations added from Check #' . $checkTotal->CheckID . ' CheckDetail #' . $checkTotal->CheckDetailID;
                    $heatNotes = (empty($heatMain->HeatNotes) ? $heatNotes : $heatMain->HeatNotes . ' :: ' . $heatNotes);
                    $this->logic->heatMain->update($heatMain->HeatNo, array(
                        'HeatNotes' => $heatNotes
                    ));
                    $message = $logPrefix . 'Updated heat notes to: ' . $heatNotes;
                    Log::debug($message);
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to update heat notes to: ' . $heatNotes . '! Exception: ' . $e->getMessage();
                    Log::error($message);
                    return array('error' => $message);
                }

                // check control panel setting for autoAddRacerToHeat
                $autoAddRacerToHeat = $this->logic->controlPanel->get('Booking', 'autoAddRacerToHeat');
                if (!empty($autoAddRacerToHeat)) {
                    $autoAddRacerToHeat = $autoAddRacerToHeat[0];
                    $autoAddRacerToHeat = Convert::convert($autoAddRacerToHeat->SettingValue, $autoAddRacerToHeat->DataType);
                }
                if ($autoAddRacerToHeat) {
                    // control panel setting signifies that we should automatically add the customer to the heat
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
                        $message = $logPrefix . 'Modified points for Customer #' . $pointHistoryReduction->CustID . ' by ' . $pointHistoryReduction->PointAmount;
                        Log::debug($message);
                    }
                    catch (\Exception $e) {
                        $message = $logPrefix . 'Unable to modify points for Customer #' . $pointHistoryReduction->CustID . ' by ' . $pointHistoryReduction->PointAmount . '! ' . $e->getMessage();
                        Log::error($message);
                        return array('error' => $message);
                    }

                    // attempt to add the customer to the heat
                    // note that sql will throw 500 exception if the HeatNo/CustID combination already exists
                    try {
                        $heatDetails                 = $this->logic->heatDetails->dummy();
                        $heatDetails->HeatNo         = $heatId;
                        $heatDetails->CustID         = $checkTotal->CustID;
                        $heatDetails->RPM            = $customer->RPM;
                        $heatDetails->PointHistoryID = $pointHistoryReductionId;
                        $this->logic->heatDetails->create($heatDetails);
                        $message = $logPrefix . 'Added Customer #' . $customer->CustID . ' to race #' . $heatId;
                        Log::debug($message);
                    }
                    catch(\Exception $e) {
                        Log::error($logPrefix . 'Unable to add Customer #' . $customer->CustID . ' to race #' . $heatId, $e);
                    }
                }
                
                // attempt to add any additional reservations by incrementing the heat's NumberOfReservation
                try {
                    $additionalReservations = $checkTotal->Qty - ($autoAddRacerToHeat ? 1 : 0); // reduce by 1, if racer was automatically added to heat
                    if (!empty($additionalReservations)) {
                        $heat                      = $this->logic->heatMain->get($heatId);
                        $heat                      = $heat[0];
                        $heat->NumberOfReservation = ($heat->NumberOfReservation ?: 0) + $additionalReservations;
                        $this->logic->heatMain->update($heat->HeatNo, $heat);
                        $message = $logPrefix . 'Added ' . $additionalReservations . ' additional reservations to race #' . $heatId;
                        Log::debug($message);
                    }
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to add ' . $additionalReservations . ' additional reservations to race #' . $heatId;
                    Log::error($message);
                    return array('error' => $message);
                }

                // clear the cache -- JUST ASSUME WE HAVE THE SHIM, IF WE ARE USING ONLINE BOOKING
                try {
                    $GLOBALS['webapi']->clearCache();
                }
                catch (\Exception $e) {
                    $message = $logPrefix . 'Unable to clear WebAPI cache! Received code: ' . $e->getCode(); // . $e->getMessage();
                    Log::error($message);
                    // return array('error' => $message); // ignore the error return during testing, pretend successful
                }

                return array(
                    'success' => $logPrefix . 'Added customer to heat #' . $heat->HeatNo
                );
            }
        }
    }
}