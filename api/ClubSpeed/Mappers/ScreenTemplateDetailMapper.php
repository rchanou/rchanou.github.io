<?php

namespace ClubSpeed\Mappers;

class ScreenTemplateDetailMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'channelDetail';
        $this->register(array(
              'ID'              => 'screenTemplateDetailId'
            , 'TemplateID'      => 'screenTemplateId'
            , 'Seq'             => 'seq'
            , 'TypeID'          => 'typeId'
            , 'TimeInSecond'    => 'timeInSecond'
            , 'Text0'           => 'text0'
            , 'Text1'           => 'text1'
            , 'Text2'           => 'text2'
            , 'Text3'           => 'text3'
            , 'Text4'           => 'text4'
            , 'Text5'           => 'text5'
            , 'Text6'           => 'text6'
            , 'Enable'          => 'enable'
            , 'TrackNo'         => 'trackNo'
        ));
    }
}