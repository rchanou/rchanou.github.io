<?php

namespace ClubSpeed\Database\Records;

class SpeedScreenChannels extends BaseRecord {

    public static $table      = 'dbo.SpeedScreenChannels';
    public static $tableAlias = 'speedScreenChannels';
    public static $key        = 'ChannelID';

    public $ChannelID;
    public $ChannelNumber;
    public $ChannelData;
    public $Created;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['ChannelID']))      $this->ChannelID       = \ClubSpeed\Utility\Convert::toNumber          ($data['ChannelID']);
                    if (isset($data['ChannelNumber']))  $this->ChannelNumber   = \ClubSpeed\Utility\Convert::toNumber          ($data['ChannelNumber']);
                    if (isset($data['ChannelData']))    $this->ChannelData     = \ClubSpeed\Utility\Convert::toString          ($data['ChannelData']);
                    if (isset($data['Created']))        $this->Created         = \ClubSpeed\Utility\Convert::toDateForServer   ($data['Created']);

                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }
}