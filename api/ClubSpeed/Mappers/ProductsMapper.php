<?php

namespace ClubSpeed\Mappers;

class ProductsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'products';
        $this->register(array(
              'ProductID'            => ''
            , 'ProductType'          => ''
            , 'Description'          => ''
            , 'Price1'               => ''
            // , 'Price2'               => ''
            // , 'Price3'               => ''
            // , 'Price4'               => ''
            // , 'Price5'               => ''
            , 'TaxID'                => ''
            , 'ProductClassID'       => ''
            // , 'LargeIcon'            => ''
            // , 'IsSpecial'            => ''
            // , 'AvailableDay'         => ''
            // , 'AvailableFromTime'    => ''
            // , 'AvailableToTime'      => ''
            // , 'IsRequiredMembership' => ''
            // , 'ShowOnWeb'            => ''
            // , 'IsTrackable'          => ''
            // , 'IsShowStat'           => ''
            // , 'IsInventory'          => ''
            // , 'Cost'                 => ''
            // , 'Req'                  => ''
            // , 'VendorID'             => ''
            , 'Enabled'              => ''
            , 'Deleted'              => ''
            // , 'P_PointTypeID'        => ''
            , 'P_Points'             => ''
            // , 'BonusValue'           => ''
            // , 'PaidValue'            => ''
            // , 'ComValue'             => ''
            // , 'Entitle1'             => ''
            // , 'Entitle2'             => ''
            // , 'Entitle3'             => ''
            // , 'Entitle4'             => ''
            // , 'Entitle5'             => ''
            // , 'Entitle6'             => ''
            // , 'Entitle7'             => ''
            // , 'Entitle8'             => ''
            // , 'M_Points'             => ''
            // , 'M_MembershiptypeID'   => ''
            , 'R_Points'             => ''
            // , 'R_LocalOnly'          => ''
            , 'G_Points'             => ''
            // , 'S_SaleBy'             => ''
            // , 'S_NoOfLapsOrSeconds'  => ''
            // , 'S_CustID'             => ''
            // , 'S_Vol'                => ''
            , 'PriceCadet'           => ''
            , 'DateCreated'          => ''
            // , 'WebShop'              => ''
        ));
    }
}