<?php

namespace ClubSpeed\Database\Collections;

class DbScreenTemplateDetail extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\ScreenTemplateDetail');
        parent::__construct($db);
        // $this->dbToJson = array(
        //       'ID'              => 'screenTemplateDetailId'
        //     , 'TemplateID'      => 'screenTemplateId'
        //     , 'Seq'             => 'seq'
        //     , 'TypeID'          => 'typeId'
        //     , 'TimeInSecond'    => 'timeInSecond'
        //     , 'Text0'           => 'text0'
        //     , 'Text1'           => 'text1'
        //     , 'Text2'           => 'text2'
        //     , 'Text3'           => 'text3'
        //     , 'Text4'           => 'text4'
        //     , 'Text5'           => 'text5'
        //     , 'Text6'           => 'text6'
        //     , 'Enable'          => 'enable'
        //     , 'TrackNo'         => 'trackNo'
        // );
        // parent::secondaryInit();
    }

    // public function compress($data = array()) {
    //     $compressed = array(
    //         'screenTemplateDetails' => array()
    //     );
    //     $screenTemplateDetails =& $compressed['screenTemplateDetails'];
    //     if (!is_array($data))
    //         $data = array($data);

    //     foreach($data as $screenTemplateDetail) {
    //         if (!empty($screenTemplateDetail))
    //             $screenTemplateDetails[] = $this->map('client', $screenTemplateDetail);
    //     }
    //     return $compressed;
    // }
}