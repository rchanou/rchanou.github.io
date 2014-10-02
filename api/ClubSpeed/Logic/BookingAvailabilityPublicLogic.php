// <?php

// namespace ClubSpeed\Logic;

// /**
//  * The business logic class
//  * for ClubSpeed online booking public availability.
//  */
// class BookingAvailabilityPublicLogic extends BaseLogic {

//     /**
//      * Constructs a new instance of the BookingAvailabilityPublicLogic class.
//      *
//      * The BookingAvailabilityPublicLogic constructor requires an instantiated DbService class for injection,
//      * as well as a reference to the LogicService container where this class will be stored.
//      *
//      * @param LogicService $logic The parent logic service container.
//      * @param DbService $db The database collection provider to inject.
//      */
//     public function __construct(&$CSLogic, &$CSDatabase) {
//         parent::__construct($CSLogic, $CSDatabase);
//         $this->interface = $this->db->onlineBookingAvailability;
//     }

//     public final function create($params = array(), $callback = null) {
//         throw new \CSException("Attempted a BookingAvailabilityPublic create!");
//     }

//     public final function update($onlineBookingsId, $params = array()) {
//         throw new \CSException("Attempted a BookingAvailabilityPublic create!");
//     }

//     public final function delete($id) {
//         throw new \CSException("Attempted a BookingAvailabilityPublic create!");
//     }

//     // public final function visible($params = array()) {
//     //     $beginningName = 'bookingAvailabilityWindowBeginningInSeconds';
//     //     $endName = 'bookingAvailabilityWindowEndInSeconds';
//     //     $now = \ClubSpeed\Utility\Convert::getDate();
//     //     $settings = $this->logic->controlPanel->find(
//     //             'SettingName = ' . $beginningName
//     //         .   ' OR SettingName = ' . $endName
//     //     );
//     //     $beginning = null;
//     //     $end = null;
//     //     foreach($settings as $setting) {
//     //         if ($setting->SettingName == $beginningName) {
//     //             $beginning = $setting;
//     //         }          
//     //         if ($setting->SettingName == $endName) {
//     //             $end = $setting;
//     //         }
//     //     }
//     //     if (is_null($beginning))
//     //         throw new \CSException("Unable to find the ControlPanel setting for Booking.bookingAvailabilityWindowBeginningInSeconds!");
//     //     if (is_null($end))
//     //         throw new \CSException("Unable to find the ControlPanel setting for Booking.bookingAvailabilityWindowEndInSeconds!");
//     //     $beginningSetting = \ClubSpeed\Utility\Convert::toNumber($beginning->SettingValue ?: $beginning->DefaultSetting);
//     //     $endSetting = \ClubSpeed\Utility\Convert::toNumber($end->SettingValue ?: $end->DefaultSetting);
//     //     $beginning = \ClubSpeed\Utility\Convert::getDate(time() + $beginningSetting);
//     //     $end = \ClubSpeed\Utility\Convert::getDate(time() + $endSetting);
//     //     $availability = $this->logic->bookingAvailability->find(
//     //         $beginning . ' <= HeatStartsAt'
//     //         . ' AND HeatStartsAt < ' . $end
//     //     );
//     //     return $availability;
//     // }

//     // public final function range($params = array()) {

//     //     // check for control panel settings, don't expose anything beyond admin panel window settings
//     //     // check for default range settings during create

//     //     if (!isset($params['start']))
//     //         $params['start'] = \ClubSpeed\Utility\Convert::toDateForServer(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');

//     //     if (!isset($params['end']))
//     //         $params['end'] = \ClubSpeed\Utility\Convert::toDateForServer(date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' + 1 days')), 'Y-m-d H:i:s');

//     //     $sqlParams = array(
//     //         ':start' => $params['start'] // make associative for tsql
//     //         , ':end' => $params['end']
//     //     );
//     //     $sql = ""
//     //         ."\nDECLARE @StartRange DATETIME2;"
//     //         ."\nDECLARE @EndRange DATETIME2;"
//     //         ."\nSET @StartRange = :start;"
//     //         ."\nSET @EndRange = :end;"
//     //         ."\nSELECT obav.*"
//     //         ."\nFROM dbo.OnlineBookingAvailability_V obav"
//     //         ."\nWHERE"
//     //         ."\n        @StartRange <= obav.HeatStartsAt"
//     //         ."\n    AND obav.HeatStartsAt < @EndRange"
//     //         ;
//     //     $records = $this->db->onlineBookingAvailability->query($sql, $sqlParams);
//     //     return $records;
//     // }
// }