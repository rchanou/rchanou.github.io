<?php

namespace ClubSpeed\Mappers;

class CheckDetailsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'checkDetails';
        $this->register(array(
              'CheckDetailID'         => ''
            , 'CheckID'               => ''
            , 'Status'                => ''
            , 'Type'                  => ''
            , 'ProductID'             => ''
            , 'ProductName'           => ''
            , 'CreatedDate'           => ''
            , 'Qty'                   => ''
            , 'UnitPrice'             => ''
            , 'UnitPrice2'            => ''
            , 'DiscountApplied'       => ''
            , 'TaxID'                 => ''
            , 'TaxPercent'            => ''
            , 'VoidNotes'             => ''
            , 'CID'                   => ''
            , 'VID'                   => ''
            , 'BonusValue'            => ''
            , 'PaidValue'             => ''
            , 'ComValue'              => ''
            , 'Entitle1'              => ''
            , 'Entitle2'              => ''
            , 'Entitle3'              => ''
            , 'Entitle4'              => ''
            , 'Entitle5'              => ''
            , 'Entitle6'              => ''
            , 'Entitle7'              => ''
            , 'Entitle8'              => ''
            , 'M_Points'              => ''
            , 'M_CustID'              => ''
            , 'M_OldMembershiptypeID' => ''
            , 'M_NewMembershiptypeID' => ''
            , 'M_Days'                => ''
            , 'M_PrimaryMembership'   => ''
            , 'P_PointTypeID'         => ''
            , 'P_Points'              => ''
            , 'P_CustID'              => ''
            , 'R_Points'              => ''
            , 'DiscountUserID'        => ''
            , 'DiscountDesc'          => ''
            , 'CalculateType'         => ''
            , 'DiscountID'            => ''
            , 'DiscountNotes'         => ''
            , 'G_Points'              => ''
            , 'G_CustID'              => ''
            , 'GST'                   => 'gst'
            , 'M_DaysAdded'           => ''
            , 'S_SaleBy'              => ''
            , 'S_NoOfLapsOrSeconds'   => ''
            , 'S_CustID'              => ''
            , 'S_Vol'                 => ''
            , 'CadetQty'              => ''
        ));
    }
}