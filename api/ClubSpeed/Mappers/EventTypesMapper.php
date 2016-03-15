<?php

namespace ClubSpeed\Mappers;

class EventTypesMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'eventTypes';
        $this->register(array(
              'EventTypeID' => 'eventTypeId'
            , 'Deleted' => 'deleted'
            , 'Description' => 'description'
            , 'DisplayAtRegistration' => 'displayAtRegistration'
            , 'Enabled' => 'enabled'
            , 'EventTypeName' => 'eventTypeName'
            , 'EventTypeTheme' => 'eventTypeTheme'
            , 'MemberOnly' => 'memberOnly'
            , 'OnlineProductID' => 'onlineProductId'
            , 'PtsPerReservation' => 'ptsPerReservation'
            , 'TrackNo' => 'trackId'
        ));
    }
}
