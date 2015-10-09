<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert;

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
        $this->logic->reservations->expire();
    }

    public final function create($params = array(), $callback = null) {
        throw new \CSException("Attempted a BookingAvailabilityLogic create!");
    }

    public final function update() {
        throw new \CSException("Attempted a BookingAvailabilityLogic create!");
    }

    public final function delete() {
        throw new \CSException("Attempted a BookingAvailabilityLogic create!");
    }

    private function getPublicDates() {
        $beginningName = 'bookingAvailabilityWindowBeginningInSeconds';
        $endName = 'bookingAvailabilityWindowEndingInSeconds';
        $now = Convert::getDate();
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
        $beginningSetting = Convert::toNumber($beginning->SettingValue ?: $beginning->DefaultSetting);
        $endSetting = Convert::toNumber($end->SettingValue ?: $end->DefaultSetting);
        $beginning = new \DateTime();
        $beginning->add(new \DateInterval('PT' . $beginningSetting . 'S'));
        $end = new \DateTime();
        $end->add(new \DateInterval('PT' . $endSetting . 'S'));
        return array(
            'beginning' => $beginning,
            'end' => $end
        );
    }

    public final function visible($params = array()) {
        $dates = $this->getPublicDates();
        $availability = $this->logic->bookingAvailability->find(
            Convert::toDateForServer($dates['beginning']) . ' <= HeatStartsAt'
            . ' AND HeatStartsAt < ' . Convert::toDateForServer($dates['end'])
        );
        return $availability;
    }

    public final function range($params = array()) {
        $publicDates = $this->getPublicDates();
        if (!isset($params['start']))
            $params['start'] = $publicDates['beginning'];
        else {
            $passedBeginningTime = new \DateTime($params['start']);
            if ($passedBeginningTime < $publicDates['beginning'])
                $params['start'] = $publicDates['beginning']; // don't allow a date range before the control panel setting
            else
                $params['start'] = $passedBeginningTime;
        }
        if (!isset($params['end'])) {
            $timeformat = 'Y-m-d H:i:s';
            $dayformat = 'Y-m-d';
            $date = new \DateTime($params['start']->format($timeformat)); // cheater copy constructor
            $date->add(new \DateInterval('P1D')); // period 1 day
            $endOfDay = new \DateTime($date->format($dayformat)); // trim time
            if ($endOfDay < $publicDates['end'])
                $params['end'] = $endOfDay;
            else
                $params['end'] = $publicDates['end'];
        }
        else {
            $passedEndTime = new \DateTime($params['end']);
            if ($passedEndTime > $publicDates['end'])
                $params['end'] = $publicDates['end']; // don't allow a date range after the control panel setting
            else
                $params['end'] = $passedEndTime;
        }
        $sqlParams = array(
              ':start' => Convert::toDateForServer($params['start']) // make associative for tsql
            , ':end'   => Convert::toDateForServer($params['end'])
        );
        $sql = ""
            ."\nDECLARE @StartRange DATETIME;"
            ."\nDECLARE @EndRange DATETIME;"
            ."\nSET @StartRange = :start;"
            ."\nSET @EndRange = :end;"
            ."\nSELECT obav.*"
            ."\nFROM dbo.OnlineBookingAvailability_V obav"
            ."\nWHERE"
            ."\n        @StartRange <= obav.HeatStartsAt"
            ."\n    AND obav.HeatStartsAt < @EndRange"
            ."\nORDER BY obav.HeatStartsAt"
            ;

        $records = $this->db->onlineBookingAvailability_V->query($sql, $sqlParams);
        return $records;
    }
}