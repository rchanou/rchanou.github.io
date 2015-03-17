<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Types as Types;

class AuthenticationTokens extends BaseRecord {
    protected static $_definition; // must be declared, so BaseRecord can use it in definition()

    public $AuthenticationTokensID;
    public $CustomersID;
    public $RemoteUserID;
    public $TokenType;
    public $Token;
    public $CreatedAt;
    public $ExpiresAt;
    public $Meta;
}