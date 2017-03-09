<?php

namespace ClubSpeed\Mappers;

class EventReservationsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventReservations';
        $this->register(array(
              'ID' => 'eventReservationId'
            , 'AllowOnlineReservation' => 'allowOnlineReservation'
            , 'CheckID' => 'checkId'
            , 'CustID' => 'customerId'
            , 'CustomerName' => 'customerName'
            , 'Deleted' => 'deleted'
            , 'Description' => 'description'
            , 'EndTime' => 'endTime'
            , 'EventTypeID' => 'eventTypeId'
            , 'IsEventClosure' => 'isEventClosure'
            , 'IsMixed' => 'isMixed'
            , 'MainID' => 'mainId'
            , 'MinNoOfAdultsPerBooking' => 'minNoOfAdultsPerBooking'
            , 'MinNoOfCadetsPerBooking' => 'minNoOfCadetsPerBooking'
            , 'NoOfCadetRacers' => 'noOfCadetRacers'
            , 'NoOfRacers' => 'noOfRacers'
            , 'NoOfTotalRacers' => 'noOfTotalRacers'
            , 'Notes' => 'notes'
            , 'PtsPerReservation' => 'ptsPerReservation'
            , 'RepID' => 'repId'
            , 'StartTime' => 'startTime'
            , 'Label' => 'status'
            // , 'Status' => 'status'
            , 'Subject' => 'subject'
            , 'TypeID' => 'typeId'
            , 'UserID' => 'userId'
            , 'ExternalSystemID' => 'externalSystemId'
        ));
    }
}
