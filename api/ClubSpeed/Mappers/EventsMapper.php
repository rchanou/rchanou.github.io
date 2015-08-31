<?php

namespace ClubSpeed\Mappers;

class EventsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'events';
        $this->register(array(
              'EventID'               => ''
            , 'EventTypeID'           => ''
            , 'MemberOnly'            => ''
            , 'EventTheme'            => ''
            , 'TotalRacers'           => ''
            , 'EventDesc'             => ''
            , 'EventTypeName'         => ''
            , 'EventDuration'         => ''
            , 'EventScheduledTime'    => ''
            , 'DisplayAtRegistration' => ''
            , 'CheckID'               => ''
            , 'IsEventClosure'        => ''
            , 'RoundNum'              => ''
            , 'EventNotes'            => ''
            , 'ReservationID'         => ''
            , 'OnlineCode'            => ''
            , 'TrackNo'               => ''
            , 'CreatedHeatSpots'      => ''
            , 'CreatedHeatTime'       => ''
            , 'TotalCadetRacers'      => ''
        ));
    }
}