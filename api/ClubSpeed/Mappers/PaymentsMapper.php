<?php

namespace ClubSpeed\Mappers;

class PaymentsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'payments';
        $this->register(array(
              'PayID'                 => 'paymentId'
            , 'CheckID'               => ''
            , 'UserID'                => ''
            , 'PayTerminal'           => ''
            , 'Shift'                 => ''
            , 'PayType'               => ''
            , 'PayDate'               => ''
            , 'PayStatus'             => ''
            , 'PayAmount'             => ''
            , 'PayTax'                => ''
            , 'VoidDate'              => ''
            , 'VoidUser'              => ''
            , 'VoidTerminal'          => ''
            , 'VoidNotes'             => ''
            , 'CheckNumber'           => ''
            , 'CheckingAccountName'   => ''
            , 'CreditCardNo'          => ''
            , 'CardType'              => ''
            , 'ExpirationDate'        => ''
            , 'AccountName'           => ''
            , 'Amount'                => ''
            , 'ResponseTime'          => ''
            , 'AuthorizationCode'     => ''
            , 'AVS'                   => ''
            , 'ReferenceNumber'       => ''
            , 'ResultCode'            => ''
            , 'TroutD'                => ''
            , 'TransactionDate'       => ''
            , 'AutAmount'             => ''
            , 'LastFour'              => ''
            , 'ExternalAccountNumber' => ''
            , 'ExternalAccountName'   => ''
            , 'VID'                   => ''
            , 'TransactionID'         => ''
            , 'BalanceRemaing'        => ''
            , 'CustID'                => 'customerId'
            , 'VoucherID'             => ''
            , 'VoucherNotes'          => ''
            , 'HistoryID'             => ''
            , 'InvoicePaidHistoryID'  => ''
            , 'ExtCardType'           => ''
            , 'Tender'                => ''
        ));
    }
}