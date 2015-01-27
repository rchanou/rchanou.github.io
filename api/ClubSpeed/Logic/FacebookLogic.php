<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Utility\Tokens as Tokens;

/**
 * The business logic class
 * for ClubSpeed facebook customers.
 */
class FacebookLogic extends BaseLogic {
    
    /**
     * Constructs a new instance of the FacebookLogic class.
     *
     * The FacebookLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->fb_customers_new;
    }

    // NOTE: we need a find method for facebook, based on a facebook unique id
    // exposed through the api for brian to use for searching

    /**
     * Logs a user in to facebook.
     *
     * @param int       $fbId                       The facebook unique id for the customer.
     * @param int       $customerId                 The customer id for the existing, underlying customer account.
     * @param string    $fbAccessToken              The access token from facebook for this user.
     * @param string    $fbAllowEmail   (optional)  A boolean indication of whether or not the account allows emails. If not provided, then assumed to be false.
     * @param string    $fbAllowPost    (optional)  A boolean indication of whether or not the account allows wall posts. If not provided, then assumed to be false.
     * @param boolean   $fbEnabled      (optional)  A boolean indication of whether or not the account is enabled. If not provided, then assumed to be true.
     *
     * @return int[string] An associative array containing the customerId for the facebook account.
     *
     * @throws InvalidArgumentException     if $fbId is not set.
     * @throws InvalidArgumentException     if $customerId is not an integer.
     * @throws InvalidArgumentException     if $fbAccessToken is not set.
     * @throws CustomerNotFoundException    if $customerId could not be found in the database.
     */
    public final function fb_login($fbId, $customerId, $fbAccessToken, $fbAllowEmail = false, $fbAllowPost = false, $fbEnabled = true) {
        
        // note; FB PHP SDK requires PHP 5.4, so we are SOL for now.
        // a hacky way to validate $fbAccessToken would be to do this:
        // GET https://graph.facebook.com/app?access_token=MY_ACCESS_TOKEN
        // a fail will get back an object with "error: {}" JSON
        // super hacky, probably not reliable forever.

        if (!isset($fbId))
            throw new \InvalidArgumentException("Facebook login requires fbId to be set!");
        if (!isset($customerId) || !is_int($customerId))
            throw new \InvalidArgumentException("Facebook login requires requires numeric customerId! Received: $customerId");
        if (!isset($fbAccessToken))
            throw new \InvalidArgumentException("Facebook login requires fbAccessToken to be set!");
        if (!$this->logic->customers->customer_exists($customerId))
            throw new \CustomerNotFoundException("Facebook login was unable to find customer in the database! Received customerId: $customerId");

        // use a merge statement to upsert into the FB_CUSTOMERS_NEW table
        $sql = "DECLARE @UId            NVARCHAR(30);   SET @UId            = :UId;"
            ."\nDECLARE @CustID         INT;            SET @CustID         = :CustID;"
            ."\nDECLARE @Access_token   NVARCHAR(300);  SET @Access_token   = :Access_token;"
            ."\nDECLARE @AllowEmail     BIT;            SET @AllowEmail     = :AllowEmail;"
            ."\nDECLARE @AllowPost      BIT;            SET @AllowPost      = :AllowPost;"
            ."\nDECLARE @Enabled        BIT;            SET @Enabled        = :Enabled;"
            ."\nMERGE INTO dbo.FB_CUSTOMERS_NEW target_table"
            ."\nUSING ("
            ."\n    SELECT"
            ."\n        @UId AS [UId]"
            ."\n        , @CustID AS [CustID]"
            ."\n) AS source_table"
            ."\nON"
            ."\n        source_table.UId    = target_table.UId"
            ."\n    AND source_table.CustID = target_table.CustID"
            ."\nWHEN MATCHED THEN"
            ."\n    UPDATE"
            ."\n    SET"
            ."\n        target_table.Access_token = @Access_token"
            ."\n        , target_table.AllowEmail = @AllowEmail"
            ."\n        , target_table.AllowPost  = @AllowPost"
            ."\n        , target_table.Enabled    = @Enabled"
            ."\nWHEN NOT MATCHED THEN"
            ."\n    INSERT ("
            ."\n        UId"
            ."\n        , CustID"
            ."\n        , Access_token"
            ."\n        , AllowEmail"
            ."\n        , AllowPost"
            ."\n        , Enabled"
            ."\n    )"
            ."\n    VALUES ("
            ."\n        @UId"
            ."\n        , @CustID"
            ."\n        , @Access_token"
            ."\n        , @AllowEmail"
            ."\n        , @AllowPost"
            ."\n        , @Enabled"
            ."\n    );" // merge must end with semi-colon
            ."\nUPDATE c"
            ."\nSET c.Privacy4 = 1" // when facebook is being used, dbo.Customers.Privacy4 is expected to be set to 1
            ."\nFROM dbo.CUSTOMERS c"
            ."\nWHERE c.CustID = @CustID;"
            ;
        $params = array(
              ':UId'            => $fbId
            , ':CustID'         => $customerId
            , ':Access_token'   => $fbAccessToken
            , ':AllowEmail'     => $fbAllowEmail
            , ':AllowPost'      => $fbAllowPost
            , ':Enabled'        => $fbEnabled
        );
        $this->db->exec($sql, $params);

        // use the logic class to match, for automatic expiry
        $authentication = $this->logic->authenticationTokens->match(array(
              'CustomersID' => $customerId
            , 'TokenType'   => Enums::TOKEN_TYPE_CUSTOMER
        ));
        if (empty($authentication)) {
            // no record found - make a new one
            $token = Tokens::generate();
            $this->logic->authenticationTokens->create(array(
                'CustomersID'       => $customerId
                , 'TokenType'       => Enums::TOKEN_TYPE_CUSTOMER
                , 'RemoteUserID'    => 1 // what to do with this?
                , 'Token'           => $token
            ));
        }
        else {
            // record was found - update it
            $authentication = $authentication[0];
            $token = $authentication->Token; // don't update the token to satisfy the case where a user logs in on multiple devices
            $this->logic->authenticationTokens->update($authentication->AuthenticationTokensID, $authentication); // logic class will update ExpiresAt automatically
        }
        return array(
              "customerId"  => $customerId
            , "token"       => $token
        );
    }
}