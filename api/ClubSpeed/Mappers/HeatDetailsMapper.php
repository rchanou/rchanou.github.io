<?php

namespace ClubSpeed\Mappers;

class HeatDetailsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'heatDetails';
        $this->register(array(
              'HeatNo'                => 'heatId'
            , 'CustID'                => 'customerId'
            , 'AutoNo'                => ''
            , 'LineUpPosition'        => ''
            , 'GroupID'               => ''
            , 'RPM'                   => 'proskill'
            , 'PointHistoryID'        => ''
            , 'FirstTime'             => ''
            , 'UserID'                => ''
            , 'FinishPosition'        => ''
            , 'GroupFinishPosition'   => ''
            , 'RPMDiff'               => 'proskillDiff'
            , 'PositionEditedDate'    => ''
            , 'HistoryAutoNo'         => ''
            , 'Scores'                => ''
            , 'TimeAdded'             => ''
            , 'AssignedtoEntitleHeat' => ''
        ));
    }
}