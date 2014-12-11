<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Mail\MailService as Mail;
use ClubSpeed\Logging\LogService as Log;
use ClubSpeed\Enums\Enums as Enums;
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
            throw new \RequiredArgumentMissingException("Password Reset did not receive an email address!");

        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $email = $params['email'];
        $customer = $this->logic->customers->find_primary_account($email); // REQUIRED
        if (is_null($customer) || empty($customer)) {
            // for security purposes, return without throwing an error
            // that way this call cannot be used 
            // to search for existing email accounts
            // based on the return type
            return;
        }

        $authentication = $this->db->authenticationTokens->dummy();
        $authentication->CustomersID = $customer->CustID;
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
        }
        else {
            $authentication->Token = Tokens::generate();
            $authentication->RemoteUserID = "RemoteUserINSERTED";
            $this->db->authenticationTokens->create($authentication);
        }

        $emailTo = array($email => $customer->FName . ' ' . $customer->LName);

        $emailFrom = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = EmailWelcomeFrom");
        if (empty($emailFrom))
            throw new \CSException("Password token create was unable to find the ControlPanel setting for MainEngine.EmailWelcomeFrom!");
        $emailFrom = $emailFrom[0];
        $emailFrom = $emailFrom->SettingValue;
        $emailFrom = explode('@', $emailFrom);
        $emailFrom = array('no-reply@' . $emailFrom[1] => "No Reply"); // safe way to do this? we don't really have a fallback value, other than clubspeed..
        
        $business = $this->logic->controlPanel->find("TerminalName = MainEngine AND SettingName = BusinessName");
        if (empty($business))
            throw new \CSException("Password token create was unable to find the ControlPanel setting for MainEngine.BusinessName!");
        $business = $business[0];
        $business = $business->SettingValue;
        $html = $this->logic->settings->match(array(
            "Namespace" => "Main",
            "Name" => "resetEmailBodyHtml"
        ));
        if (empty($html))
            throw new \CSException("Password token create was unable to find the setting setting for Main.resetEmailBodyHtml!");
        $html = $html[0];
        $html = $html->Value;
        $url = 'https://' . $_SERVER['SERVER_NAME'] . '/booking/resetpassword/form?token=' . urlencode($authentication->Token);
        $html = strtr($html, array(
            "{{url}}" => $url,
            "{{business}}" => $business
        ));

        $text = $this->logic->settings->match(array(
            "Namespace" => "Main",
            "Name" => "resetEmailBodyText"
        ));
        if (empty($text))
            throw new \CSException("Password token create was unable to find the setting setting for Main.resetEmailBodyHtml!");
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
        try {
            Mail::send($mail);
            Log::debug("Sent password reset email to: " . $customer->EmailAddress);
        }
        catch(\Exception $e) {
            Log::error("Unable to send password reset email to: " . $customer->EmailAddress, $e); 
            throw $e; // catch the exception to log it, then rethrow / consider this a fatal error
        }
    }

    public final function reset($params = array()) {
        // if (is_null($params['email']) || empty($params['email']))
            // throw new \RequiredArgumentMissingException("Password reset requires email!");
        if (is_null($params['token']) || empty($params['token']))
            throw new \RequiredArgumentMissingException("Password reset requires token!");
        if (is_null($params['password']) || empty($params['password']))
            throw new \RequiredArgumentMissingException("Password reset requires password!");

        // $email      = $params['email'];
        $token      = $params['token'];
        $password   = $params['password'];

        // validate the token by searching for the entry on the database
        $results = $this->db->authenticationTokens->match(array(
            'Token' => $token
        ));
        if (count($results) < 1)
            throw new \InvalidTokenException("Invalid token authentication was supplied to the API!");
        $authentication = $results[0];

        // find the customer
        $customerId = $authentication->CustomersID;
        $customer = $this->db->customers->get($customerId);
        if (is_null($customer) || empty($customer))
            throw new \CustomerNotFoundException("Password reset was unable to find the requested customer!");
        $customer = $customer[0];
        // update the customer
        $customer->Password = \ClubSpeed\Security\Hasher::hash($password);
        $affected = $this->db->customers->update($customer);

        // delete the token record
        $this->delete($authentication->AuthenticationTokensID);

        // success - return anything other than 200?
    }

    public final function delete(/* authenticationId */) {
        $args = func_get_args();
        $authenticationId = $args[0];
        $affected = $this->db->authenticationTokens->delete($authenticationId);
    }
}