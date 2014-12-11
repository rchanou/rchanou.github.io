<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Utility\Convert as Convert;

/**
 * The business logic class
 * for ClubSpeed authentication tokens.
 */
class AuthenticationTokensLogic extends BaseLogic {

    private $expiresAtInterval;

    /**
     * Constructs a new instance of the AuthenticationTokensLogic class.
     *
     * The AuthenticationTokensLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->authenticationTokens;
        $this->expire(); // expire on any authenticationTokens construct
        $this->expiresAtInterval = "+24 hours";
    }

    public function create($params = array()) {
        $logic =& $this->logic;
        $expiresAtInterval = $this->expiresAtInterval;
        return parent::_create($params, function($authenticationToken) use (&$logic, $expiresAtInterval) {
            $authenticationToken->CreatedAt = Convert::getDate(); // db also has a default
            $authenticationToken->ExpiresAt = Convert::getDate(strtotime($expiresAtInterval));
            return $authenticationToken;
        });
    }

    public function update(/* $id, $params = array() */) {
        $args = func_get_args();
        $logic =& $this->logic;
        $expiresAtInterval = $this->expiresAtInterval;
        $closure = function($old, $new) use (&$logic, $expiresAtInterval) {
            $new->ExpiresAt = Convert::getDate(strtotime($expiresAtInterval));
            return $new;
        };
        array_push($args, $closure);
        return call_user_func_array(array("parent", "update"), $args);
    }

    public function expire() {
        // use a direct sql statement for performance purposes
        $sql = ""
            ."\nDELETE at"
            ."\nFROM dbo.AuthenticationTokens at"
            ."\nWHERE"
            ."\n        at.ExpiresAt IS NOT NULL"
            ."\n    AND at.ExpiresAt < GETDATE()"
            ;
        $this->db->exec($sql);
    }
}