<?php

namespace ClubSpeed\Payments\ProductHandlers;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Utility\Convert as Convert;

class ReservationProductHandler extends BaseProductHandler {

    public function __construct(&$logic) {
        parent::__construct($logic);
    }

    public function handle($checkTotal, $metadata = array()) {
        $logPrefix = "Check #" . $checkTotal->CheckID . ": CheckDetail #" . $checkTotal->CheckDetailID . ": ";
        $now = \ClubSpeed\Utility\Convert::getDate();

        if (empty($metadata)) {
            $message = $logPrefix . 'No metadata passed to ReservationProductHandler';
            Log::error($message);
            // then we are done processing, return the message
            return array('error' => $message); // is this an error? will we ever have a reservation product handler without a heatId?
        }
        else {
            if (!isset($metadata['heatId'])) {
                $message = $logPrefix . 'No heatId passed to ReservationProductHandler';
                Log::error($message);
                return array('error' => $message); // is this an error or a success?
            }
            else {
                $heatId = null;
                $checkNotes = '';
                $heatNotes = '';
                $remainingQty = $checkTotal->Qty;

                // check for heat existence
                try {
                    $heatId     = Convert::toNumber($metadata['heatId']);
                    $heatMain   = $this->logic->heatMain->get($heatId);
                    $heatMain   = $heatMain[0];
                    $checkNotes = $checkTotal->Qty . ' racers for Heat #' . $heatId . ' at ' . $heatMain->ScheduledTime . ' on Track #' . $heatMain->TrackNo;
                    $message    = $logPrefix . 'Heat #' . $heatId . ' passed with check details metadata';
                    Log::info($message);
                }
                catch (\Exception $e) {
                    $message = $logPrefix . 'Unable to find Heat #' . $heatId . '! ' . $e->getMessage();
                    Log::error($message);
                    return array('error' => $message);
                }

                // check for customer existence
                try {
                    $customer = $this->logic->customers->get($checkTotal->CustID);
                    $customer = $customer[0];
                    // log anything?
                }
                catch (\Exception $e) {
                    $message = $logPrefix . 'Unable to find Customer #'. $checkTotal->CustID . '! ' . $e->getMessage();
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
                    Log::info($message);
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to update check notes to: ' . $checkNotes . '! Exception: ' . $e->getMessage();
                    Log::error($message);
                    // non blocking, continue with logic
                }

                // update the heat notes for business sanity check
                try {
                    $heatNotes = $customer->FName . ' ' . $customer->LName . ' ReservationID #' . $checkTotal->CheckID . ' Qty: ' . $checkTotal->Qty;
                    $heatNotes = (empty($heatMain->HeatNotes) ? $heatNotes : $heatMain->HeatNotes . ', ' . $heatNotes);
                    $this->logic->heatMain->update($heatMain->HeatNo, array(
                        'HeatNotes' => $heatNotes
                    ));
                    $message = $logPrefix . 'Updated heat notes to: ' . $heatNotes;
                    Log::info($message);
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to update heat notes to: ' . $heatNotes . '! Exception: ' . $e->getMessage();
                    Log::error($message);
                    // non blocking, continue with logic
                }

                // check control panel setting for autoAddRacerToHeat
                $autoAddRacerToHeat = true; // default to true
                try {
                    $autoAddRacerToHeat = $this->logic->controlPanel->get('Booking', 'autoAddRacerToHeat');
                    if (!empty($autoAddRacerToHeat)) {
                        $autoAddRacerToHeat = $autoAddRacerToHeat[0];
                        $autoAddRacerToHeat = Convert::convert($autoAddRacerToHeat->SettingValue, $autoAddRacerToHeat->DataType);
                    }
                }
                catch(\Exception $e) {
                    // logic classes with throw exceptions on null gets.
                    // we don't really care about this exception, other than catching it.
                }

                if ($autoAddRacerToHeat) {
                    // control panel setting signifies that we should automatically add the customer to the heat
                    
                    if ($checkTotal->R_Points > 0) {

                        // make a point history record to signify that the reservation was reduced
                        try {
                            $pointHistoryReservation                    = $this->logic->pointHistory->dummy();
                            $pointHistoryReservation->CheckDetailID     = 0; // required by front end
                            $pointHistoryReservation->CheckID           = Enums::DB_NULL; // required by front end
                            $pointHistoryReservation->CustID            = 0; // required by front end
                            $pointHistoryReservation->Notes             = 'Transfer points from CheckID ' . $checkTotal->CheckID;
                            $pointHistoryReservation->PointAmount       = -1 * $checkTotal->R_Points;
                            $pointHistoryReservation->PointDate         = $now;
                            $pointHistoryReservation->PointExpDate      = Convert::toDateForServer('2038-01-18');
                            $pointHistoryReservation->ReferenceID       = 0; // used when transferring points from one customer to another
                            $pointHistoryReservation->RefPointHistoryID = 0; // required by front end
                            $pointHistoryReservation->ReservationID     = $checkTotal->CheckID; // required by front end
                            $pointHistoryReservation->Type              = Enums::POINT_HISTORY_TRANSFER_FOR_RESERVATION; // consider this a transfer off the check?
                            $pointHistoryReservation->UserID            = 1;
                            $pointHistoryReservation->Username          = 'api';
                            $pointHistoryReservationId = $this->logic->pointHistory->create($pointHistoryReservation);
                            $pointHistoryReservationId = $pointHistoryReservationId['PointHistoryID'];
                            $message = $logPrefix . 'Modified points for Reservation Check #' . $checkTotal->CheckID . ' by ' . $pointHistoryReservation->PointAmount;
                            Log::info($message);
                        }
                        catch (\Exception $e) {
                            $message = $logPrefix . 'Unable to reduce points for Reservation Check #' . $checkTotal->CheckID . "! " . $e->getMessage();
                            Log::error($message);
                            return array('error' => $message);
                        }
                        
                        // make a point history record to add points to the customer
                        try {
                            $pointHistory                    = $this->logic->pointHistory->dummy();
                            $pointHistory->CheckDetailID     = $checkTotal->CheckDetailID;
                            $pointHistory->CheckID           = Enums::DB_NULL; // required by front end
                            $pointHistory->CustID            = $checkTotal->CustID;
                            $pointHistory->Notes             = 'CheckID ' . $checkTotal->CheckID;
                            $pointHistory->PointAmount       = $checkTotal->R_Points;
                            $pointHistory->PointDate         = $now;
                            $pointHistory->PointExpDate      = Convert::toDateForServer('2038-01-18');
                            $pointHistory->ReferenceID       = 0; // used when transferring points from one customer to another
                            $pointHistory->RefPointHistoryID = $pointHistoryReservationId; // required by front end
                            $pointHistory->ReservationID     = 0; // required by the front end
                            $pointHistory->Type              = Enums::POINT_HISTORY_TRANSFER_FOR_RESERVATION; // consider this a transfer off the check?
                            $pointHistory->UserID            = 1;
                            $pointHistory->Username          = 'api';
                            $this->logic->pointHistory->create($pointHistory);
                            $message = $logPrefix . 'Modified points for Customer #' . $pointHistory->CustID . ' by ' . $pointHistory->PointAmount;
                            Log::info($message);
                        }
                        catch (\Exception $e) {
                            $message = $logPrefix . 'Unable to add points to Customer #' . $checkTotal->CustID . "! " . $e->getMessage();
                            Log::error($message);
                            return array('error' => $message);
                        }

                        // make a point history record to remove those same points from the customer
                        try {
                            $pointHistoryReduction                    = $this->logic->pointHistory->dummy();
                            $pointHistoryReduction->CheckDetailID     = 0; // required by the front end
                            $pointHistoryReduction->CheckID           = Enums::DB_NULL;
                            $pointHistoryReduction->CustID            = $checkTotal->CustID;
                            $pointHistoryReduction->Notes             = 'HeatNo ' . $heatId;
                            $pointHistoryReduction->PointAmount       = -1 * $checkTotal->R_Points;
                            $pointHistoryReduction->PointDate         = $now;
                            $pointHistoryReduction->PointExpDate      = \ClubSpeed\Utility\Convert::toDateForServer('2038-01-18');
                            $pointHistoryReduction->ReferenceID       = $heatMain->HeatNo; // required by the front end!!
                            $pointHistoryReduction->RefPointHistoryID = 0; // required by the front end
                            $pointHistoryReduction->ReservationID     = 0; // required by the front end
                            $pointHistoryReduction->Type              = Enums::POINT_HISTORY_HEAT;
                            $pointHistoryReduction->UserID            = 1;
                            $pointHistoryReduction->Username          = 'api';
                            $pointHistoryReductionId                  = $this->logic->pointHistory->create($pointHistoryReduction);
                            $pointHistoryReductionId                  = $pointHistoryReductionId['PointHistoryID'];
                            $message                                  = $logPrefix . 'Modified points for Customer #' . $pointHistoryReduction->CustID . ' by ' . $pointHistoryReduction->PointAmount;
                            Log::info($message);
                        }
                        catch (\Exception $e) {
                            $message = $logPrefix . 'Unable to modify points for Customer #' . $pointHistoryReduction->CustID . ' by ' . $pointHistoryReduction->PointAmount . '! ' . $e->getMessage();
                            Log::error($message);
                            return array('error' => $message);
                        }
                    }

                    // attempt to add the customer to the heat, regardless of the R_Points quantity (since some races will be reservations with 0 points)
                    try {
                        $heatDetailExists = $this->logic->heatDetails->exists($heatId, $checkTotal->CustID);
                        if ($heatDetailExists) {
                            Log::info($logPrefix . 'Customer #' . $checkTotal->CustID . ' already exists on the HeatDetails for Heat #' . $heatId);
                            // keep the remaining qty the same - we need to add this as a reservation, since they are making a double+ purchase for the same heat
                        }
                        else {
                            $heatDetails                 = $this->logic->heatDetails->dummy();
                            $heatDetails->HeatNo         = $heatId;
                            $heatDetails->CustID         = $checkTotal->CustID;
                            $heatDetails->RPM            = $customer->RPM;
                            $heatDetails->PointHistoryID = ($checkTotal->R_Points > 0) ? $pointHistoryReductionId : null; // this is unavailable unless the item has points(!)
                            $this->logic->heatDetails->create($heatDetails);
                            $remainingQty = $remainingQty - 1; // chop one off the remaining quantity, since one person has been added
                            $message = $logPrefix . 'Added Customer #' . $customer->CustID . ' to race #' . $heatId;
                            Log::info($message);
                        }
                    }
                    catch(\Exception $e) {
                        $message = $logPrefix . 'Unable to add Customer #' . $customer->CustID . ' to race #' . $heatId . '! ' . $e->getMessage();
                        Log::error($message);
                        return array('error' => $message);
                    }
                }
                else
                    Log::info($logPrefix . 'Customer #' . $customer->CustID . ' was not added directly to race #' . $heatId . ' since the Booking.autoAddRacerToHeat setting was false!');

                // attempt to add any additional reservations by incrementing the heat's NumberOfReservation
                try {
                    if (!empty($remainingQty)) { // use the running total, since autoaddracertoheat doesn't necessarily signify that we will reduce by 1
                        $heat                      = $this->logic->heatMain->get($heatId);
                        $heat                      = $heat[0];
                        $heat->NumberOfReservation = ($heat->NumberOfReservation ?: 0) + $remainingQty;
                        $this->logic->heatMain->update($heat->HeatNo, $heat);
                        $message = $logPrefix . 'Added ' . $remainingQty . ' additional reservations to race #' . $heatId;
                        Log::info($message);
                    }
                }
                catch(\Exception $e) {
                    $message = $logPrefix . 'Unable to add ' . $remainingQty . ' additional reservations to race #' . $heatId . '! ' . $e->getMessage();
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

                try {
                    $dateDisplayFormat = $this->logic->controlPanel->get('Booking', 'dateDisplayFormat'); // logic classes throw exceptions on failed gets
                    $dateDisplayFormat = $dateDisplayFormat[0];
                    $dateDisplayFormat = $dateDisplayFormat->SettingValue ?: $dateDisplayFormat->DefaultSetting;
                }
                catch (\Exception $e) {
                    $dateDisplayFormat = 'Y-m-d';
                }
                try {
                    $timeDisplayFormat = $this->logic->controlPanel->get('Booking', 'timeDisplayFormat');
                    $timeDisplayFormat = $timeDisplayFormat[0];
                    $timeDisplayFormat = $timeDisplayFormat->SettingValue ?: $timeDisplayFormat->DefaultSetting;
                }
                catch (\Exception $e) {
                    $timeDisplayFormat = 'H.i'; // fallback!
                }

                $scheduledTime = new \DateTime($heatMain->ScheduledTime);
                return array(
                    'success' => 'Heat #' . $heatId . ' scheduled at ' . $scheduledTime->format($dateDisplayFormat . ' ' . $timeDisplayFormat)
                );
            }
        }
    }
}