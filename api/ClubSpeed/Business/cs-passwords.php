<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'../../Utility/Tokens.php');
require_once(__DIR__.'../../Utility/Convert.php');


/**
 * The business logic class
 * for ClubSpeed passwords and tokens.
 */
class CSPasswords {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSPasswords class.
     *
     * The CSPasswords constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
    }


    public final function create($params = array()) {
        if (empty($params) || is_null($params['email']) || empty($params['email']))
            throw new \RequiredArgumentMissingException("Password Reset did not receive an email address!");

        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->onlineBookingReservations->map('server', $params);
        $email = $params['email'];
        $customer = $this->logic->customers->find_primary_account($email); // ALSO REQUIRED
        if (is_null($customer) || empty($customer)) {
            // for security purposes, return without throwing an error
            // that way this call cannot be used 
            // to search for existing email accounts
            // based on the return type
            return;
        }
        $authentication = $this->db->authenticationTokens->blank();
        $authentication->CustomersID = $customer['CustID'];
        $authentication->TokenType = 'PasswordReset';
        $existingAuthentications = $this->find($authentication);
        if (!empty($existingAuthentications)) {
            // should we update the existing token here?
            // or consider this an error for security purposes?
            // probably update the existing token -- seems to be how most sites handle this
            $authentication = $existingAuthentications[0];
            $authentication->Token = \ClubSpeed\Utility\Tokens::generate();
            $authentication->RemoteUserID = "RemoteUserUPDATED2";
            $affected = $this->db->authenticationTokens->update($authentication);
        }
        else {
            $authentication->Token = \ClubSpeed\Utility\Tokens::generate();
            $authentication->RemoteUserID = "RemoteUserINSERTED";
            $this->db->authenticationTokens->create($authentication);
        }

        // email needs to be sent at this point,
        // with a link pointing back to necessary url for brian's front end,
        // passing along the token as part of the query string (??)
    }

    public final function get($id) {
        // TODO (if necessary)
        // $authentication = new \ClubSpeed\Database\Classes\AuthenticationTokens($id); // build dummy record with only an id
        $get = $this->db->onlineBookingAvailability->get($onlineBookingsId);
        return $get;
    }

    public final function find($params = array()) {
        return $this->db->authenticationTokens->find($params);


        // if ($params instanceof \ClubSpeed\Database\Classes\AuthenticationTokens)
        //     $authentication = $params;
        // else
        //     $authentication = new \ClubSpeed\Database\Classes\AuthenticationTokens($params);

        // $results = $this->cs->find($authentication);
        // $authentications = array();
        // foreach($results as $result) {
        //     $authentications[] = new \ClubSpeed\Database\Classes\AuthenticationTokens($result);
        // }
        // return $authentications;
    }

    public final function update($id, $params = array()) {

    }

    public final function validate($params = array()) {

    }

    public final function reset($params = array()) {
        if (is_null($params['email']) || empty($params['email']))
            throw new \RequiredArgumentMissingException("Password reset requires email!");
        if (is_null($params['token']) || empty($params['token']))
            throw new \RequiredArgumentMissingException("Password reset requires token!");
        if (is_null($params['password']) || empty($params['password']))
            throw new \RequiredArgumentMissingException("Password reset requires password!");
        $email      = $params['email'];
        $token      = $params['token'];
        $password   = $params['password'];

        $authentication = $this->db->authenticationTokens->blank();
        $authentication->Token = $token;

        // validate the token
        $results = $this->db->authenticationTokens->find($authentication);
        if (count($results) < 1) {
            throw new \InvalidTokenException("Invalid token authentication was supplied to the API!");
        }
        die();

        // find the customer
        $authentication->load($results[0]);
        pr($authentication);
        die();
        $customerId = $authentication->CustomersID;
        $customer = $this->db->customers->get($customerId);
        pr($customer);
        die();
        if (is_null($customer) || empty($customer)) {
            throw new \CustomerNotFoundException("Password reset was unable to find the requested customer!");
        }

        // update the customer
        $customer->Password = \ClubSpeed\Security\Hasher::hash($password);
        $affected = $this->db->customers->update($customer);

        // delete the token record
        $this->delete($authenticationId);

        die();


        // update the customer password, if validated

        // remove the token / record, if password updated

        // return success
    }

    public final function delete($authenticationId) {

        $this->authenticationTokens->delete($authenticationId);

        if (!isset($onlineBookingsId))
            throw new \InvalidArgumentException("Delete online booking requires an online bookings id!");
        $sql = "DELETE ob"
            ."\nFROM dbo.OnlineBookings ob"
            ."\nWHERE ob.OnlineBookingsID = :OnlineBookingsID"
            ;
        $params = array(
            ":OnlineBookingsID" => $onlineBookingsId
        );
        $affected = $this->cs->exec($sql, $params);
    }
}