<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed online booking availability.
 */
class BookingAvailabilityLogic extends BaseLogic {

    /**
     * Constructs a new instance of the BookingAvailabilityLogic class.
     *
     * The BookingAvailabilityLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->onlineBookingAvailability_V;
    }

    public final function create($params = array(), $callback = null) {
        throw new \CSException("Attempted a BookingAvailabilityLogic create!");
    }

    public final function update($onlineBookingsId, $params = array()) {
        throw new \CSException("Attempted a BookingAvailabilityLogic create!");
    }

    public final function delete($id) {
        throw new \CSException("Attempted a BookingAvailabilityLogic create!");
    }

    private function getPublicDates() {
        $beginningName = 'bookingAvailabilityWindowBeginningInSeconds';
        $endName = 'bookingAvailabilityWindowEndingInSeconds';
        $now = \ClubSpeed\Utility\Convert::getDate();
        $settings = $this->logic->controlPanel->find(
                'SettingName = ' . $beginningName
            .   ' OR SettingName = ' . $endName
        );
        $beginning = null;
        $end = null;
        foreach($settings as $setting) {
            if ($setting->SettingName == $beginningName) {
                $beginning = $setting;
            }          
            if ($setting->SettingName == $endName) {
                $end = $setting;
            }
        }
        if (is_null($beginning))
            throw new \CSException("Unable to find the ControlPanel setting for Booking." . $beginningName . "!");
        if (is_null($end))
            throw new \CSException("Unable to find the ControlPanel setting for Booking." . $endName . "!");
        $beginningSetting = \ClubSpeed\Utility\Convert::toNumber($beginning->SettingValue ?: $beginning->DefaultSetting);
        $endSetting = \ClubSpeed\Utility\Convert::toNumber($end->SettingValue ?: $end->DefaultSetting);
        $beginning = \ClubSpeed\Utility\Convert::getDate(time() + $beginningSetting);
        $end = \ClubSpeed\Utility\Convert::getDate(time() + $endSetting);
        return array(
            'beginning' => $beginning,
            'end' => $end
        );
    }

    public final function visible($params = array()) {
        $dates = $this->getPublicDates();
        $availability = $this->logic->bookingAvailability->find(
            $dates['beginning'] . ' <= HeatStartsAt'
            . ' AND HeatStartsAt < ' . $dates['end']
        );
        return $availability;
    }

    public final function range($params = array()) {
        $publicDates = $this->getPublicDates();
        if (!isset($params['start']))
            $params['start'] = $publicDates['beginning'];
        else {
            $passedBeginningTime = strtotime($params['start']);
            $publicBeginningTime = strtotime($publicDates['beginning']);
            if ($passedBeginningTime < $publicBeginningTime)
                $params['start'] = $publicDates['beginning']; // don't allow a date range past the control panel setting
        }
        if (!isset($params['end'])) {
            $timeformat = 'Y-m-d H:i:s';
            $dayformat = 'Y-m-d';
            $params['end'] = \ClubSpeed\Utility\Convert::toDateForServer(date($timeformat, strtotime(date($dayformat) . ' + 1 day')), $timeformat);
        }
        else {
            $passedEndTime = strtotime($params['end']);
            $publicEndTime = strtotime($publicDates['end']);
            if ($passedEndTime > $publicEndTime)
                $params['end'] = $publicDates['end']; // don't allow a date range past the control panel setting
        }

        $sqlParams = array(
            ':start' => $params['start'] // make associative for tsql
            , ':end' => $params['end']
        );
        $sql = ""
            ."\nDECLARE @StartRange DATETIME2;"
            ."\nDECLARE @EndRange DATETIME2;"
            ."\nSET @StartRange = :start;"
            ."\nSET @EndRange = :end;"
            ."\nSELECT obav.*"
            ."\nFROM dbo.OnlineBookingAvailability_V obav"
            ."\nWHERE"
            ."\n        @StartRange <= obav.HeatStartsAt"
            ."\n    AND obav.HeatStartsAt < @EndRange"
            ;
        $records = $this->db->onlineBookingAvailability_V->query($sql, $sqlParams);
        return $records;
    }
}