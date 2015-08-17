<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Utility\Types;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Utility\Arrays;

/**
 * The business logic class
 * for ClubSpeed payments.
 */
class PaymentLogic extends BaseLogic {

    /**
     * Constructs a new instance of the PaymentLogic class.
     *
     * The PaymentLogic constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the LogicContainer from which this class will been loaded.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->payment;

        $this->before('uow', function($uow) use ($db) {
            switch($uow->action) {
                case 'create':
                    if (!empty($uow->data)) {
                        $payment =& $uow->data;
                        if (is_null($payment->PayAmount) || Convert::convert($payment->PayAmount, Types::$integer) === 0)
                            throw new \RequiredArgumentMissingException('Attempted to create a payment record without a PayAmount!');
                        if (is_null($payment->CheckID))
                            throw new \RequiredArgumentMissingException('Attempted to create a payment record without a CheckID!');
                        $check = $db->checkTotals_V->get($payment->CheckID);
                        $check = $check[0];
                        if (empty($check))
                            throw new \InvalidArgumentValueException('Attempted to create a payment record with an invalid CheckID! Received: ' . $payment->CheckID);
                        if ($payment->PayAmount > $check->CheckRemainingTotal)
                            throw new \InvalidArgumentValueException('Attempted to create a payment record with an overpayment for CheckID! PayAmount: ' . $payment->PayAmount . ' :: CheckRemainingTotal: ' . (int)$check->CheckRemainingTotal);
                        if (is_null($payment->UserID))
                            $payment->UserID = 1;
                        if (is_null($payment->VoidTerminal))
                            $payment->VoidTerminal = '';
                        if (is_null($payment->ExternalAccountNumber))
                            $payment->ExternalAccountNumber = ''; // front end logs error if this is set to null
                        if (is_null($payment->ExternalAccountName))
                            $payment->ExternalAccountName = ''; // front end logs error if this is set to null, and payment type is 3 -- just default to empty
                        if (is_null($payment->PayStatus))
                            $payment->PayStatus = Enums::PAY_STATUS_PAID;
                        if (is_null($payment->PayTerminal))
                            $payment->PayTerminal = '';
                        if (is_null($payment->VoidTerminal))
                            $payment->VoidTerminal = '';
                        if (is_null($payment->PayType))
                            $payment->PayType = Enums::PAY_TYPE_EXTERNAL;
                        if (is_null($payment->PayDate))
                            $payment->PayDate = Convert::getDate();
                        if (is_null($payment->PayTax)) {
                            $sql = "EXEC dbo.CalculateTaxPayment :CheckID, :PaymentAmount";
                            $params = array(
                                  ":CheckID"       => $payment->CheckID
                                , ":PaymentAmount" => $payment->PayAmount
                            );
                            $result = $db->query($sql, $params);
                            $result = $result[0];
                            $taxes = $result['Taxes'];
                            $payment->PayTax = $taxes;
                        }
                    }
                    break;
            }
        });
    }

    public function void($id, $data = array()) {
        $payment = $this->logic->payment->get($id);
        $payment = Arrays::first($payment);
        if ($payment->PayStatus === Enums::PAY_STATUS_VOID)
            return; // break early, already voided
        $uow = UnitOfWork::build()
            ->action('update')
            ->table_id($id)
            ->data(array(
                'PayStatus' => Enums::PAY_STATUS_VOID,
                'VoidDate' => Convert::getDate(),
                'VoidNotes' => 'Voided through the API'
            ));
        return $this->uow($uow);
    }
}