<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Database\Helpers\UnitOfWork;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Payments\ProductHandlers\ProductHandlerService as Handlers;
use ClubSpeed\Templates\TemplateService as Templates;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Utility\Currency;

/**
 * The business logic class
 * for ClubSpeed checks.
 */
class ChecksLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ChecksLogic class.
     *
     * The ChecksLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->checks;

        $this->insertable = array(
              'CustID'
            , 'CheckType'
            // , 'CheckStatus'
            , 'CheckName'
            , 'UserID'
            // , 'CheckTotal'
            , 'BrokerName'
            , 'Notes'
            , 'Gratuity'
            , 'Fee'
            // , 'OpenedDate'
            // , 'ClosedDate'
            , 'IsTaxExempt'
            , 'Discount'
            , 'DiscountID'
            , 'DiscountNotes'
            , 'DiscountUserID'
            , 'InvoiceDate'
        );

        $this->updatable = array(
              // 'CustID'
              'CheckType'
            , 'CheckStatus'
            , 'CheckName'
            , 'UserID'
            // , 'CheckTotal'
            , 'BrokerName'
            , 'Notes'
            , 'Gratuity'
            , 'Fee'
            // , 'OpenedDate'
            , 'ClosedDate'
            , 'IsTaxExempt'
            , 'Discount'
            , 'DiscountID'
            , 'DiscountNotes'
            , 'DiscountUserID'
            , 'InvoiceDate'
        );
    }

    // override and check for foreign keys, apply defaults
    public function create($params = array()) {
        $db =& $this->db;
        // note that in 5.4+, we can just reference $this inside the closure
        // and then $this can properly access private and protected items
        $return = parent::_create($params, function($check) use (&$db) {
            // validate physical structure before checking for foreign keys

            // validate the customer "foreign key", as the database does not actually have a foreign key
            $customer = $db->customers->get($check->CustID);
            if (is_null($customer))
                throw new \RecordNotFoundException('Customers', $check->CustID);
            
            // validate the user "foreign key", as the database does not actually have a foreign key
            if (empty($check->UserID))
                $check->UserID = 1; // just cheat and use 1
            $user = $db->users->get($check->UserID);
            if (is_null($user))
                throw new \RecordNotFoundException('Users', $check->UserID);
            $check->CheckType = Enums::CHECK_TYPE_REGULAR;
            $check->CheckStatus = Enums::CHECK_STATUS_OPEN;
            $check->OpenedDate = Convert::getDate();
            
            return $check; // use reference instead of return?
        });
        $this->applyCheckTotal($return['CheckID']); // safe?
        return $return;
    }

    public function update() {
        $args = func_get_args();
        $return = call_user_func_array('parent::update', $args);
        $this->applyCheckTotal($args[0]); // safe?
        return $return;
    }

    public function void($checkId) {
        // also include void message?
        $check = $this->interface->get($checkId);
        $check = $check[0];
        $voidNotes = 'Voided from API at ' . Convert::getDate();

        $notes = $check->Notes;
        if (!empty($notes) && is_string($notes))
            $notes = trim($notes);
        $check->Notes = (empty($notes) ? $voidNotes : ($notes . ' :: ' . $voidNotes));

        $check->CheckStatus = Enums::CHECK_STATUS_CLOSED;
        $this->interface->update($check);
        $checkDetails = $this->db->checkDetails->match(array(
            'CheckID' => $checkId
        ));
        foreach($checkDetails as $checkDetail) {
            $checkDetail->Status = Enums::CHECK_DETAIL_STATUS_HAS_VOIDED;
            $checkDetail->VoidNotes = (empty($checkDetail->VoidNotes) ? '' : ' :: ' ) . $voidNotes;
            $this->db->checkDetails->update($checkDetail);
        }
        // // what to do with any existing payments? anything? prevent voiding check if they exist? void the payments?
        // $payments = $this->db->payment->match(array(
        //     'CheckID' => $checkId
        // ));

        // what about any point history?
        // gift card history?

        // pr($payments);
        // die();
    }

    public function applyCheckTotal($id) {
        if (!isset($id) || is_null($id))
            throw new \RequiredArgumentMissingException("Checks ApplyCheckTotal received an empty CheckID!");
        $sql = "EXEC dbo.ApplyCheckTotal :checkId";
        $params = array(":checkId" => $id);
        return $this->db->exec($sql, $params);
    }

    public function finalize($checkId, $data = array()) {
        $metadata = @$data['details'] ?: array();
        $logPrefix = 'Check #' . $checkId . ': ';
        Log::info($logPrefix . 'Beginning finalize', Enums::NSP_API);
        $check = $this->get($checkId); // throws 404 on missing
        $check = $check[0];

        // do we want to enforce that the check be open before running finalize?
        if ($check->CheckStatus == Enums::CHECK_STATUS_CLOSED) {
            $message = 'Attempted to run finalize on closed Check #' . $checkId . '!';
            Log::error($logPrefix . $message, Enums::NSP_API);
            throw new \InvalidArgumentValueException($message);
        }

        $uow = UnitOfWork::build()
            ->action('all')
            ->where(array(
                'CheckID' => $checkId
            ));
        $checkTotals = $this->db->checkTotals_V->uow($uow)->data;
        $checkTotal = Arrays::first($checkTotals);

        // do we want to enforce that the check is fully paid off before running finalize?
        if ($checkTotal->CheckRemainingTotal > 0) {
            $message = 'Attempted to run finalize on Check #'. $checkId . ' which is not balanced! Remaining: ' . $checkTotal->CheckRemainingTotal;
            Log::error($logPrefix . $message, Enums::NSP_API);
            throw new \InvalidArgumentValueException($message);
        }

        $handled = Handlers::handle($checkTotals, $metadata); // can throw, but finalized is wrapped in try/catch from restler level
        Log::info('Check #' . $checkId . ': Finalize complete!', Enums::NSP_API);

        // attempt to close the check by default? probably should (R_Points won't be closed).
        $this->close($checkId);
        $receiptData = $this->receipt($checkId, $handled, @$data['sendCustomerReceiptEmail']);
        return $receiptData;
    }

    public function close($checkId) {
        try {
            $check = $this->interface->get($checkId);
            $check = $check[0];
            $logPrefix = 'Check #' . $checkId. ': ';

            $checkStatus = Enums::CHECK_STATUS_CLOSED;
            $closedDate = Convert::getDate();
            $checkDetails = $this->logic->checkDetails->find(
                'CheckID $eq ' . $checkId
            );
            foreach ($checkDetails as $checkDetail) {
                if (!is_null($checkDetail->R_Points)) {
                    $checkStatus = Enums::CHECK_STATUS_OPEN; // if any line items have R_Points as non-null, then we have to leave the check open for the front end (!!!)
                    $closedDate = Enums::DB_NULL;
                }
                $this->logic->checkdetails->update($checkDetail->CheckDetailID, array(
                    'Status' => Enums::CHECK_DETAIL_STATUS_CANNOT_DELETED
                ));
            }
            $check = $this->logic->checks->get($checkId);
            $check = $check[0];
            $check->CheckStatus = $checkStatus;
            $check->ClosedDate = $closedDate;
            $this->logic->checks->update($check->CheckID, $check);
            Log::info($logPrefix . 'Closed check', Enums::NSP_BOOKING);
        }
        catch (\Exception $e) {
            Log::error($logPrefix . 'Unable to close check!', Enums::NSP_BOOKING, $e);
            throw $e;
        }
    }

    public function receipt($checkId, $handled = array(), $sendCustomerReceiptEmail) {
        // this isn't super helpful as an exposed api endpoint,
        // since $handled is calculated entirely at runtime,
        // and has no storage point in the db for lookup
        $logPrefix = 'Check #' . $checkId . ': ';
        $checkTotals = $this->logic->checkTotals->get($checkId); // refresh only returns the single object, we want all check details items as well
        $receiptData = array();
        try {
            $businessName = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = BusinessName");
            $businessName = $businessName[0];
            $businessName = $businessName->SettingValue;

            $checkTotal = Arrays::first($checkTotals);
            $customer = $this->logic->customers->get($checkTotal->CustID);
            $customer = $customer[0];
            $emailTo  = array($customer->EmailAddress => $customer->FName . ' ' . $customer->LName);

            $emailFrom = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = EmailWelcomeFrom");
            $emailFrom = $emailFrom[0];
            $emailFrom = array($emailFrom->SettingValue => $businessName);

            $receiptData = array(
                  'checkId'       => $checkTotal->CheckID
                , 'customer'      => $customer->FName . ' ' . $customer->LName
                , 'email'         => $customer->EmailAddress
                , 'business'      => $businessName
                , 'checkSubtotal' => Currency::toCurrencyString($checkTotal->CheckSubtotal)
                , 'checkTotal'    => Currency::toCurrencyString($checkTotal->CheckTotal) // why did we use remaining? for gift cards?
                , 'checkTax'      => Currency::toCurrencyString($checkTotal->CheckTax)
                , 'balance'       => Currency::toCurrencyString($checkTotal->CheckRemainingTotal)
                , 'details'       => array()
                , 'payments'      => array()
            );
            foreach($checkTotals as $checkTotal) {
                // see if we can just use the check note instead of this handler nonsense.

                $handlerResult = Arrays::first($handled, function($x) use ($checkTotal) {
                    return $x['checkDetailId'] === $checkTotal->CheckDetailID;
                });
                $note = (empty($handlerResult) ? '' : $handlerResult['description']);
                $heatId = isset($handlerResult['heatId']) ? $handlerResult['heatId'] : null;
                $scheduledTime = isset($handlerResult['scheduledTime']) ? $handlerResult['scheduledTime'] : null;
                $trackName = isset($handlerResult['trackName']) ? $handlerResult['trackName'] : null;

                // $note = (isset($handled[$checkTotal->CheckDetailID]) && !empty($handled[$checkTotal->CheckDetailID])) ? $handled[$checkTotal->CheckDetailID] : '';
                $productName = !empty($checkTotal->ProductName) ? $checkTotal->ProductName : '(No product name!)';
                $receiptData['details'][] = array(
                      'checkDetailId' => $checkTotal->CheckDetailID
                    , 'note'          => $note
                    , 'productName'   => $productName
                    , 'description'   => trim($productName . ': ' . $note) // for backwards compatibility and convenience
                    , 'quantity'      => $checkTotal->Qty
                    , 'price'         => Currency::toCurrencyString($checkTotal->CheckDetailSubtotal / $checkTotal->Qty) // use CheckDetailSubtotal or UnitPrice (coming from the product table)
                    , 'heatId'        => $heatId
                    , 'scheduledTime' => $scheduledTime
                    , 'trackName'     => $trackName
                );
            }
            $checkPayments = $this->logic->payment->match(array(
                'CheckID' => $checkId
            ));
            foreach($checkPayments as $checkPayment) {
                $type = null;
                switch($checkPayment->PayType) {
                    case Enums::PAY_TYPE_GIFT_CARD:
                        $type = 'Gift Card';
                        break;
                    case Enums::PAY_TYPE_EXTERNAL:
                        $type = 'External';
                        break;
                    default:
                        $type = 'Default';
                        break;
                }
                $receiptData['payments'][] = array(
                    'type' => $type,
                    'amount' => Currency::toCurrencyString($checkPayment->PayAmount)
                );
            }

            $receiptEmailBodyHtml = $this->logic->settings->match(array(
                'Namespace' => 'Booking',
                'Name' => 'receiptEmailBodyHtml'
            ));
            $receiptEmailBodyHtml = $receiptEmailBodyHtml[0];
            $receiptEmailBodyHtml = $receiptEmailBodyHtml->Value;
            // $receiptEmailBodyHtml = file_get_contents(__DIR__.'/../../migrations/resources/201411041100 - HTML01 - receipt.html'); // for testing purposes
            $receiptEmailBodyHtml = Templates::buildFromString($receiptEmailBodyHtml, $receiptData);

            $receiptEmailBodyText = $this->logic->settings->match(array(
                'Namespace' => 'Booking',
                'Name' => 'receiptEmailBodyText'
            ));
            $receiptEmailBodyText = $receiptEmailBodyText[0];
            $receiptEmailBodyText = $receiptEmailBodyText->Value;
            // $receiptEmailBodyText = file_get_contents(__DIR__.'/../../migrations/resources/201411041100 - TEXT01 - receipt.txt'); // for testing purposes
            $receiptEmailBodyText = Templates::buildFromString($receiptEmailBodyText, $receiptData); // use twig to build the text template -- hacky, but works fine (note: don't use HTML comments in the text file for the twig logic, it doesn't get parsed properly)

            $receiptEmailSubject = $this->logic->settings->match(array(
                'Namespace' => 'Booking',
                'Name' => 'receiptEmailSubject'
            ));
            $receiptEmailSubject = $receiptEmailSubject[0];
            $receiptEmailSubject = $receiptEmailSubject->Value;
            $receiptEmailSubject = str_replace(
                array('{{businessName}}', '{{checkId}}'),
                array($businessName, $checkId),
                $receiptEmailSubject
            );

            try {
                $sendCustomerReceiptEmail = isset($sendCustomerReceiptEmail) ? Convert::toBoolean($sendCustomerReceiptEmail) : true; // default to true
                if (!$sendCustomerReceiptEmail)
                    Log::info($logPrefix . 'API Call has opted out of sending the customer a receipt email!', Enums::NSP_BOOKING);
                else
                    Log::info($logPrefix . 'Receipt will be emailed to Customer #' . $customer->CustID . ' at ' . $customer->EmailAddress, Enums::NSP_BOOKING);

                $sendReceiptCopyTo = $this->logic->controlPanel->match(array(
                    'TerminalName' => 'Booking',
                    'SettingName' => 'sendReceiptCopyTo'
                ));
                if (!empty($sendReceiptCopyTo)) {
                    $sendReceiptCopyTo = $sendReceiptCopyTo[0];
                    $sendReceiptCopyTo = $sendReceiptCopyTo->SettingValue; // this will default to an empty string
                    if (!empty($sendReceiptCopyTo)) {
                        $sendReceiptCopyTo = explode(",", $sendReceiptCopyTo); // we should be able to use this directly as the BCC
                        array_walk($sendReceiptCopyTo, function(&$val) {
                            $val = trim($val); // get rid of any additional whitespace in the comma-delimited list of emails
                        });
                    }
                }
                if (!empty($sendReceiptCopyTo)) // check again to see if its still empty
                    Log::info($logPrefix . 'Receipt copies will be sent to the following addresses: ' . print_r($sendReceiptCopyTo, true), Enums::NSP_BOOKING);
                else
                    Log::info($logPrefix . 'No BCC list was found for receipt copies!', Enums::NSP_BOOKING);

                // check to see if we have no override, or a BCC list
                // if we have either or, then attempt to send the mail
                if ($sendCustomerReceiptEmail || !empty($sendReceiptCopyTo)) {
                    $mail = Mail::builder()
                        ->subject($receiptEmailSubject)
                        ->from($emailFrom)
                        ->body($receiptEmailBodyHtml)
                        ->alternate($receiptEmailBodyText);
                    if ($sendCustomerReceiptEmail)
                        $mail->to($emailTo);
                    if (!empty($sendReceiptCopyTo))
                        $mail->bcc($sendReceiptCopyTo);
                    Mail::send($mail);
                    Log::info($logPrefix . 'Receipt email was sent', Enums::NSP_BOOKING);
                }
                else
                    Log::info($logPrefix . 'Receipt email was not sent, since no valid BCC list was found and the API opted out of sending the customer a receipt!', Enums::NSP_BOOKING);
            }
            catch (\Exception $e) {
                Log::error($logPrefix . "Receipt email could not be sent to Customer #" . $customer->CustID . '!', Enums::NSP_BOOKING, $e);
            }
        }
        catch (\Exception $e) {
            Log::error($logPrefix . "Receipt email could not be built!", Enums::NSP_BOOKING, $e);
        }
        return $receiptData;
    }
}