<?php

namespace ClubSpeed\Mappers;

class SpeedScreenChannelsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'speedScreenChannels';
        $this->register(array(
          'ChannelID'     => ''
        , 'ChannelNumber' => ''
        , 'ChannelData'   => ''
        , 'Created'       => ''
        ));
    }
}