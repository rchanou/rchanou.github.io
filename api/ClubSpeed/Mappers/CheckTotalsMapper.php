<?php

namespace ClubSpeed\Mappers;

class CheckTotalsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'checks';
        $this->register(array(
              'CheckID'                 => '' // check items start here
            , 'CustID'                  => 'customerId'
            , 'CheckType'               => ''
            , 'CheckStatus'             => ''
            , 'CheckName'               => 'name'
            , 'UserID'                  => ''
            , 'CheckTotalApplied'       => ''
            , 'BrokerName'              => 'broker'
            , 'Notes'                   => ''
            , 'Gratuity'                => ''
            , 'Fee'                     => ''
            , 'OpenedDate'              => ''
            , 'ClosedDate'              => ''
            , 'IsTaxExempt'             => ''
            , 'Discount'                => ''
            , 'CheckSubtotal'           => ''
            , 'CheckTax'                => ''
            , 'CheckTotal'              => ''
            , 'CheckDetailID'           => '' // check detail items start here
            , 'CheckDetailStatus'       => ''
            , 'CheckDetailType'         => ''
            , 'ProductID'               => ''
            , 'ProductName'             => ''
            , 'CreatedDate'             => ''
            , 'Qty'                     => ''
            , 'UnitPrice'               => ''
            , 'UnitPrice2'              => ''
            , 'DiscountApplied'         => ''
            , 'TaxID'                   => ''
            , 'TaxPercent'              => ''
            , 'VoidNotes'               => ''
            , 'CID'                     => ''
            , 'VID'                     => ''
            , 'BonusValue'              => ''
            , 'PaidValue'               => ''
            , 'ComValue'                => ''
            , 'Entitle1'                => ''
            , 'Entitle2'                => ''
            , 'Entitle3'                => ''
            , 'Entitle4'                => ''
            , 'Entitle5'                => ''
            , 'Entitle6'                => ''
            , 'Entitle7'                => ''
            , 'Entitle8'                => ''
            , 'M_Points'                => ''
            , 'M_CustID'                => ''
            , 'M_OldMembershiptypeID'   => ''
            , 'M_NewMembershiptypeID'   => ''
            , 'M_Days'                  => ''
            , 'M_PrimaryMembership'     => ''
            , 'P_PointTypeID'           => ''
            , 'P_Points'                => ''
            , 'P_CustID'                => ''
            , 'R_Points'                => ''
            , 'DiscountUserID'          => ''
            , 'DiscountDesc'            => ''
            , 'CalculateType'           => ''
            , 'DiscountID'              => ''
            , 'DiscountNotes'           => ''
            , 'G_Points'                => ''
            , 'G_CustID'                => ''
            , 'GST'                     => 'gst'
            , 'M_DaysAdded'             => ''
            , 'S_SaleBy'                => ''
            , 'S_NoOfLapsOrSeconds'     => ''
            , 'S_CustID'                => ''
            , 'S_Vol'                   => ''
            , 'CadetQty'                => ''
            , 'CheckDetailSubtotal'     => ''
            , 'CheckDetailTax'          => ''
            , 'CheckDetailTotal'        => ''
        ));
    }

    public final function limit($type, $select = array()) {
        $clientKey = $this->map('client', 'CheckID');
        if (!is_array($select) || empty($select))
            parent::limit($type, $select);
        else {
            if (!in_array($clientKey, $select))
                // don't allow the user to attempt to remove the key right now,
                // as compress does its grouping by using the key
                $select[] = $clientKey;
            parent::limit($type, $select);
        }
    }

    protected final function decompress($data) {
        $decompressed = array();
        if (!empty($data)) {
            if (isset($data[$this->namespace])) {
                $i = 0; // record counter;
                foreach($data[$this->namespace] as $record) {
                    if (!isset($record[($this->map('client', 'CheckID'))]))
                        // note this lovely/horrible bit of hack -- 
                        // give the check a temporary checkId if not provided
                        // in order to be able to compress/decompress to and from
                        // JSON <-> SQL Records while maintaining foreign key structure
                        $record[($this->map('client', 'CheckID'))] = $i++;
                    if (isset($record['details']) && !empty($record['details'])) {
                        $details = $record['details']; // pull details off of the record
                        unset($record['details']); // store details separately
                        foreach($details as $detail) {
                            $decompressed[] = array_merge($record, $detail);
                        }
                    }
                }
            }
            else {
                return $data; // hackish way to allow match to still work
            }
        }
        return $decompressed;
    }

    protected final function compress($data = array(), $select = array()) {
        $return = array(
            $this->namespace => array()
        );
        $checks =& $return[$this->namespace];
        if (isset($data) && !is_array($data))
            $data = array($data); // convert a single record to an array for the foreach syntax to function

        if (!is_null($data)) {
            if ($this->is_assoc($data)) { // for the id => {id} arrays coming from create calls. this seems hacky -- consider another option
                foreach($data as $key => $value) {
                    $compressed[$this->map('client', $key)] = $value;
                }
                return $compressed;
            }
            else {

                // we need to split check and checkdetails into their own arrays of keys for this type of compress
                // and take into account the select filters passed in by the client (if any)
                $checkKeys = array(
                      'CheckID'
                    , 'CustID'
                    , 'CheckType'
                    , 'CheckStatus'
                    , 'CheckName'
                    , 'UserID'
                    , 'CheckTotalApplied'
                    , 'BrokerName'
                    , 'Notes'
                    , 'Gratuity'
                    , 'Fee'
                    , 'OpenedDate'
                    , 'ClosedDate'
                    , 'IsTaxExempt'
                    , 'Discount'
                    , 'CheckSubtotal'
                    , 'CheckTax'
                    , 'CheckTotal'
                );
                $detailKeys = array(
                      'CheckDetailID'
                    , 'CheckDetailStatus'
                    , 'CheckDetailType'
                    , 'ProductID'
                    , 'ProductName'
                    , 'CreatedDate'
                    , 'Qty'
                    , 'UnitPrice'
                    , 'UnitPrice2'
                    , 'DiscountApplied'
                    , 'TaxID'
                    , 'TaxPercent'
                    , 'VoidNotes'
                    , 'CID'
                    , 'VID'
                    , 'BonusValue'
                    , 'PaidValue'
                    , 'ComValue'
                    , 'Entitle1'
                    , 'Entitle2'
                    , 'Entitle3'
                    , 'Entitle4'
                    , 'Entitle5'
                    , 'Entitle6'
                    , 'Entitle7'
                    , 'Entitle8'
                    , 'M_Points'
                    , 'M_CustID'
                    , 'M_OldMembershiptypeID'
                    , 'M_NewMembershiptypeID'
                    , 'M_Days'
                    , 'M_PrimaryMembership'
                    , 'P_PointTypeID'
                    , 'P_Points'
                    , 'P_CustID'
                    , 'R_Points'
                    , 'DiscountUserID'
                    , 'DiscountDesc'
                    , 'CalculateType'
                    , 'DiscountID'
                    , 'DiscountNotes'
                    , 'G_Points'
                    , 'G_CustID'
                    , 'GST'
                    , 'M_DaysAdded'
                    , 'S_SaleBy'
                    , 'S_NoOfLapsOrSeconds'
                    , 'S_CustID'
                    , 'S_Vol'
                    , 'CadetQty'
                    , 'CheckDetailSubtotal'
                    , 'CheckDetailTax'
                    , 'CheckDetailTotal'
                );
                $map = $this->_map['client']; // pull the map here to cut down on function calls
                $checkKeys = array_values(array_intersect(array_keys($map), $checkKeys)); // get the filtered list of columns for checks
                $detailKeys = array_values(array_intersect(array_keys($map), $detailKeys)); // get the filtered list of columns for checkdetails
                foreach($data as $row) {
                    $existingCheck =& self::findExisting($checks, $map['CheckID'], $row->CheckID);
                    if ($existingCheck == null) {
                        $existingCheck = array();
                        foreach($checkKeys as $key)
                            $existingCheck[$map[$key]] = $row->{$key};
                        $existingCheck['details'] = array();
                        $checks[] =& $existingCheck;
                    }
                    foreach($detailKeys as $key)
                        $details[$map[$key]] = $row->{$key};
                    if (!empty($details) && !\ClubSpeed\Utility\Objects::isEmpty($details)) {
                        $existingCheck['details'][] = $details;
                    }
                }
            }
        }
        return $return;
    }
}