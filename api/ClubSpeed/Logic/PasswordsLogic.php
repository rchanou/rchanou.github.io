<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Utility\Tokens as Tokens;

/**
 * The business logic class
 * for ClubSpeed passwords and tokens.
 */
class PasswordsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the PasswordsLogic class.
     *
     * The PasswordsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->authenticationTokens;
    }

    public final function create($params = array()) {
        if (empty($params) || is_null($params['email']) || empty($params['email']))
            throw new \RequiredArgumentMissingException("Password reset did not receive an email address!");
        try {
            $params = \ClubSpeed\Utility\Params::nonReservedData($params);
            $email = $params['email'];
            $customer = $this->logic->customers->find_primary_account($email); // REQUIRED
            $customerId = $customer->CustID;
            if (is_null($customer) || empty($customer)) {
                Log::warn('Password reset received an email address which could not be found! Received: ' . $email, Enums::NSP_PASSWORD);
                // for security purposes, return without throwing an error
                // that way this call cannot be used 
                // to search for existing email accounts
                // based on the return type
                return;
            }
            Log::info('Password reset logic being executed for Customer ID: ' . $customerId . ' at email: ' . $email);
            $authentication = $this->db->authenticationTokens->dummy();
            $authentication->CustomersID = $customerId;
            $authentication->TokenType = Enums::TOKEN_TYPE_PASSWORD_RESET;
            $existingAuthentications = $this->db->authenticationTokens->find($authentication);
            if (!empty($existingAuthentications)) {
                // should we update the existing token here?
                // or consider this an error for security purposes?
                // probably update the existing token -- seems to be how most sites handle this
                $authentication = $existingAuthentications[0];
                $authentication->Token = Tokens::generate();
                $authentication->RemoteUserID = "RemoteUserUPDATED";
                $affected = $this->db->authenticationTokens->update($authentication);
                Log::info('Password reset updated token for Customer ID: ' . $customerId, Enums::NSP_PASSWORD);
            }
            else {
                $authentication->RemoteUserID = "RemoteUserINSERTED";
                $authentication->Token = Tokens::generate();
                $this->db->authenticationTokens->create($authentication);
                Log::info('Password reset inserted new token for Customer ID: ' . $customerId, Enums::NSP_PASSWORD);
            }

            $emailTo = array($email => $customer->FName . ' ' . $customer->LName);

            // grab email address information for the track, using the EmailWelcomeFrom setting
            $emailFrom = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = EmailWelcomeFrom");
            if (empty($emailFrom))
                Log::error('Password token create was unable to find the ControlPanel setting for MainEngine.EmailWelcomeFrom!', Enums::NSP_PASSWORD);
            $emailFrom = $emailFrom[0];
            $emailFrom = $emailFrom->SettingValue;
            $emailFrom = explode('@', $emailFrom);
            $emailFrom = array('no-reply@' . $emailFrom[1] => "No Reply"); // safe way to do this? we don't really have a fallback value, other than clubspeed..
            
            // grab business name
            $business = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = BusinessName");
            if (empty($business))
                throw new \CSException('Password token create was unable to find the setting for Main.resetEmailBodyHtml!');
            $business = $business[0];
            $business = $business->SettingValue;

            // grab html template
            $html = $this->logic->settings->match(array(
                "Namespace" => "Main",
                "Name" => "resetEmailBodyHtml"
            ));
            if (empty($html))
                throw new \CSException('Password token create was unable to find the setting for Main.resetEmailBodyHtml!');
            $html = $html[0];
            $html = $html->Value;
            $url = 'https://' . $_SERVER['SERVER_NAME'] . '/booking/resetpassword/form?token=' . urlencode($authentication->Token);
            $html = strtr($html, array(
                "{{url}}" => $url,
                "{{business}}" => $business
            ));

            // grab text template
            $text = $this->logic->settings->match(array(
                "Namespace" => "Main",
                "Name" => "resetEmailBodyText"
            ));
            if (empty($text))
                throw new \CSException('Password token create was unable to find the setting for Main.resetEmailBodyText!');
            $text = $text[0];
            $text = $text->Value;
            $text = strtr($text, array(
                "{{url}}" => $url,
                "{{business}}" => $business
            ));

            $mail = Mail::builder()
                ->subject("Password Reset for " . $business)
                ->from($emailFrom)
                ->to($emailTo)
                ->body($html)
                ->alternate($text);
            Mail::send($mail);
            Log::info("Sent password reset email to: " . $customer->EmailAddress . ' for Customer ID: ' . $customer->CustID, Enums::NSP_PASSWORD);
        }
        catch (\Exception $e) {
            Log::error('Password reset encountered an exception!', Enums::NSP_PASSWORD, $e);
            throw $e; // rethrow after logging, consider fatal
        }
    }

    public final function reset($params = array()) {
        // if (is_null($params['email']) || empty($params['email']))
            // throw new \RequiredArgumentMissingException("Password reset requires email!");
        if (is_null($params['token']) || empty($params['token']))
            throw new \RequiredArgumentMissingException("Password reset requires token!");
        if (is_null($params['password']) || empty($params['password']))
            throw new \RequiredArgumentMissingException("Password reset requires password!");

        $token      = $params['token'];
        $password   = $params['password'];

        // validate the token by searching for the entry on the database
        $results = $this->db->authenticationTokens->match(array(
            'Token' => $token
        ));
        if (count($results) < 1) {
            $message = 'Password reset was attempted with an invalid or expired token!';
            Log::error($message . ' Received: ' . $token, Enums::NSP_PASSWORD);
            throw new \InvalidTokenException($message);
        }
        $authentication = $results[0];

        // find the customer
        $customerId = $authentication->CustomersID;
        $customer = $this->db->customers->get($customerId);
        if (is_null($customer) || empty($customer))
            throw new \CustomerNotFoundException("Password reset was unable to find the requested customer! Looking for Customer ID: " . $customerId);
        $customer = $customer[0];
        // update the customer
        try {
            $customer->Hash = \ClubSpeed\Security\Hasher::hash($password);
            $affected = $this->db->customers->update($customer);
            Log::info('Password reset has updated password for Customer ID: ' . $customer->CustID, Enums::NSP_PASSWORD);
        }
        catch (\Exception $e) {
            Log::error('Password reset was unable to update password for Customer ID: ' . $customer->CustID, Enums::NSP_PASSWORD, $e);
            throw $e; // rethrow -- we want to log this, but still consider it an exception
        }
        // delete the token record
        $this->delete($authentication->AuthenticationTokensID); // can put inside try/catch if necessary.

        // success - return anything other than 200?
    }

    public final function delete(/* authenticationId */) {
        $args = func_get_args();
        $authenticationId = $args[0];
        $affected = $this->db->authenticationTokens->delete($authenticationId);
    }
}