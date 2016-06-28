<?php

namespace ClubSpeed\Mappers;
use ClubSpeed\Utility\Arrays;

class CheckTotalsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'checks';
        $this->register(array(
              'CheckID'                          => '' // check items start here
            , 'CustID'                           => 'customerId'
            , 'CheckType'                        => ''
            , 'CheckStatus'                      => ''
            , 'CheckName'                        => 'name'
            , 'UserID'                           => ''
            , 'CheckTotalApplied'                => ''
            , 'BrokerName'                       => 'broker'
            , 'Notes'                            => ''
            , 'Gratuity'                         => ''
            , 'Fee'                              => ''
            , 'OpenedDate'                       => ''
            , 'ClosedDate'                       => ''
            , 'IsTaxExempt'                      => ''
            , 'Discount'                         => ''
            , 'CheckDiscountID'                  => ''
            , 'CheckDiscountNotes'               => ''
            , 'CheckDiscountUserID'              => ''
            , 'CheckSubtotal'                    => ''
            , 'CheckTax'                         => ''
            , 'CheckGST'                         => ''
            , 'CheckPST'                         => ''
            , 'CheckTotal'                       => ''
            , 'CheckPaidTax'                     => ''
            , 'CheckPaidTotal'                   => ''
            , 'CheckRemainingTax'                => ''
            , 'CheckRemainingTotal'              => ''
            , 'CheckDetailID'                    => '' // check detail items start here
            , 'CheckDetailStatus'                => ''
            , 'CheckDetailType'                  => ''
            , 'ProductID'                        => ''
            , 'ProductName'                      => ''
            , 'CreatedDate'                      => ''
            , 'Qty'                              => ''
            , 'UnitPrice'                        => ''
            , 'UnitPrice2'                       => ''
            , 'DiscountApplied'                  => ''
            , 'CheckDetailDiscountUserID'        => ''
            , 'CheckDetailDiscountDesc'          => ''
            , 'CheckDetailDiscountCalculateType' => ''
            , 'CheckDetailDiscountID'            => ''
            , 'CheckDetailDiscountNotes'         => ''
            , 'TaxID'                            => ''
            , 'TaxPercent'                       => ''
            , 'VoidNotes'                        => ''
            , 'CID'                              => ''
            , 'VID'                              => ''
            , 'BonusValue'                       => ''
            , 'PaidValue'                        => ''
            , 'ComValue'                         => ''
            , 'Entitle1'                         => ''
            , 'Entitle2'                         => ''
            , 'Entitle3'                         => ''
            , 'Entitle4'                         => ''
            , 'Entitle5'                         => ''
            , 'Entitle6'                         => ''
            , 'Entitle7'                         => ''
            , 'Entitle8'                         => ''
            , 'M_Points'                         => ''
            , 'M_CustID'                         => ''
            , 'M_OldMembershiptypeID'            => ''
            , 'M_NewMembershiptypeID'            => ''
            , 'M_Days'                           => ''
            , 'M_PrimaryMembership'              => ''
            , 'P_PointTypeID'                    => ''
            , 'P_Points'                         => ''
            , 'P_CustID'                         => ''
            , 'R_Points'                         => ''
            , 'G_Points'                         => ''
            , 'G_CustID'                         => ''
            , 'GST'                              => 'gst'
            , 'M_DaysAdded'                      => ''
            , 'S_SaleBy'                         => ''
            , 'S_NoOfLapsOrSeconds'              => ''
            , 'S_CustID'                         => ''
            , 'S_Vol'                            => ''
            , 'CadetQty'                         => ''
            , 'CheckDetailSubtotal'              => ''
            , 'CheckDetailTax'                   => ''
            , 'CheckDetailGST'                   => ''
            , 'CheckDetailPST'                   => ''
            , 'CheckDetailTotal'                 => ''
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
        $inner =& $return[$this->namespace];
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
                $inner = Arrays::group($data, function($val) {
                    return array(
                        'CheckID' => $val->CustID
                    );
                });
                foreach($inner as $key => $group) {
                    $self =& $this; // php 5.3 nonsense
                    $inner[$key] = array_reduce($group, function($carry, $current) use (&$self) {
                        if (!is_array($carry)) {
                            $carry = $self->map('client', array(
                                'CustID'              => $current->CustID,
                                'CheckID'             => $current->CheckID,
                                'CustID'              => $current->CustID,
                                'CheckType'           => $current->CheckType,
                                'CheckStatus'         => $current->CheckStatus,
                                'CheckName'           => $current->CheckName,
                                'UserID'              => $current->UserID,
                                'CheckTotalApplied'   => $current->CheckTotalApplied,
                                'BrokerName'          => $current->BrokerName,
                                'Notes'               => $current->Notes,
                                'Gratuity'            => $current->Gratuity,
                                'Fee'                 => $current->Fee,
                                'OpenedDate'          => $current->OpenedDate,
                                'ClosedDate'          => $current->ClosedDate,
                                'IsTaxExempt'         => $current->IsTaxExempt,
                                'Discount'            => $current->Discount,
                                'CheckDiscountID'     => $current->CheckDiscountID,
                                'CheckDiscountNotes'  => $current->CheckDiscountNotes,
                                'CheckDiscountUserID' => $current->CheckDiscountUserID,
                                'CheckSubtotal'       => $current->CheckSubtotal,
                                'CheckTax'            => $current->CheckTax,
                                'CheckGST'            => $current->CheckGST,
                                'CheckPST'            => $current->CheckPST,
                                'CheckTotal'          => $current->CheckTotal,
                                'CheckPaidTax'        => $current->CheckPaidTax,
                                'CheckPaidTotal'      => $current->CheckPaidTotal,
                                'CheckRemainingTax'   => $current->CheckRemainingTax,
                                'CheckRemainingTotal' => $current->CheckRemainingTotal,
                                'details'             => array()
                            ));
                        }

                        $detail = $self->map('client', array(
                            'CheckDetailID'                     => $current->CheckDetailID,
                            'CheckDetailStatus'                 => $current->CheckDetailStatus,
                            'CheckDetailType'                   => $current->CheckDetailType,
                            'ProductID'                         => $current->ProductID,
                            'ProductName'                       => $current->ProductName,
                            'CreatedDate'                       => $current->CreatedDate,
                            'Qty'                               => $current->Qty,
                            'UnitPrice'                         => $current->UnitPrice,
                            'UnitPrice2'                        => $current->UnitPrice2,
                            'DiscountApplied'                   => $current->DiscountApplied,
                            'TaxID'                             => $current->TaxID,
                            'TaxPercent'                        => $current->TaxPercent,
                            'VoidNotes'                         => $current->VoidNotes,
                            'CID'                               => $current->CID,
                            'VID'                               => $current->VID,
                            'BonusValue'                        => $current->BonusValue,
                            'PaidValue'                         => $current->PaidValue,
                            'ComValue'                          => $current->ComValue,
                            'Entitle1'                          => $current->Entitle1,
                            'Entitle2'                          => $current->Entitle2,
                            'Entitle3'                          => $current->Entitle3,
                            'Entitle4'                          => $current->Entitle4,
                            'Entitle5'                          => $current->Entitle5,
                            'Entitle6'                          => $current->Entitle6,
                            'Entitle7'                          => $current->Entitle7,
                            'Entitle8'                          => $current->Entitle8,
                            'M_Points'                          => $current->M_Points,
                            'M_CustID'                          => $current->M_CustID,
                            'M_OldMembershiptypeID'             => $current->M_OldMembershiptypeID,
                            'M_NewMembershiptypeID'             => $current->M_NewMembershiptypeID,
                            'M_Days'                            => $current->M_Days,
                            'M_PrimaryMembership'               => $current->M_PrimaryMembership,
                            'P_PointTypeID'                     => $current->P_PointTypeID,
                            'P_Points'                          => $current->P_Points,
                            'P_CustID'                          => $current->P_CustID,
                            'R_Points'                          => $current->R_Points,
                            'CheckDetailDiscountUserID'         => $current->CheckDetailDiscountUserID,
                            'CheckDetailDiscountDesc'           => $current->CheckDetailDiscountDesc,
                            'CheckDetailDiscountCalculateType'  => $current->CheckDetailDiscountCalculateType,
                            'CheckDetailDiscountID'             => $current->CheckDetailDiscountID,
                            'CheckDetailDiscountNotes'          => $current->CheckDetailDiscountNotes,
                            'G_Points'                          => $current->G_Points,
                            'G_CustID'                          => $current->G_CustID,
                            'GST'                               => $current->GST,
                            'M_DaysAdded'                       => $current->M_DaysAdded,
                            'S_SaleBy'                          => $current->S_SaleBy,
                            'S_NoOfLapsOrSeconds'               => $current->S_NoOfLapsOrSeconds,
                            'S_CustID'                          => $current->S_CustID,
                            'S_Vol'                             => $current->S_Vol,
                            'CadetQty'                          => $current->CadetQty,
                            'CheckDetailSubtotal'               => $current->CheckDetailSubtotal,
                            'CheckDetailTax'                    => $current->CheckDetailTax,
                            'CheckDetailGST'                    => $current->CheckDetailGST,
                            'CheckDetailPST'                    => $current->CheckDetailPST,
                            'CheckDetailTotal'                  => $current->CheckDetailTotal
                        ));
                        
                        if (!empty($detail))
                            $carry['details'][] = $detail;
                        return $carry;
                    });
                }
            }
        }
        return $return;
    }
}