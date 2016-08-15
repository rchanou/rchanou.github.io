<?php

namespace ClubSpeed\Mappers;

class PaymentMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'payments';
        $this->register(array(
              'PayID'                 => 'paymentId'
            , 'CheckID'               => ''
            , 'UserID'                => ''
            , 'PayTerminal'           => ''
            // , 'Shift'                 => ''
            , 'PayType'               => ''
            , 'PayDate'               => ''
            , 'PayStatus'             => ''
            , 'PayAmount'             => ''
            , 'PayTax'                => ''
            , 'VoidDate'              => ''
            , 'VoidUser'              => ''
            , 'VoidTerminal'          => ''
            , 'VoidNotes'             => ''
            // , 'CheckNumber'           => ''
            // , 'CheckingAccountName'   => ''
            // , 'CreditCardNo'          => ''
            , 'CardType'              => ''
            // , 'ExpirationDate'        => ''
            // , 'AccountName'           => ''
            // , 'Amount'                => ''
            // , 'ResponseTime'          => ''
            // , 'AuthorizationCode'     => ''
            // , 'AVS'                   => 'avs'
            // , 'ReferenceNumber'       => ''
            // , 'ResultCode'            => ''
            , 'TroutD'                => 'troutd'
            // , 'TransactionDate'       => ''
            // , 'AutAmount'             => ''
            // , 'LastFour'              => ''
            // , 'ExternalAccountNumber' => ''
            // , 'ExternalAccountName'   => ''
            // , 'VID'                   => ''
            // , 'TransactionID'         => 'transaction'
            // , 'BalanceRemaing'        => 'balanceRemaining'
            , 'CustID'                => 'customerId'
            , 'VoucherID'             => ''
            , 'VoucherNotes'          => ''
            , 'HistoryID'             => ''
            , 'InvoicePaidHistoryID'  => ''
            , 'ExtCardType'           => ''
            , 'Tender'                => ''
            , 'TransactionReference'  => 'transaction'
        ));
    }
}