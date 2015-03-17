<?php

namespace ClubSpeed\Database\Records;

class FB_Customers_New extends BaseRecord {
    protected static $_definition;

    public $FB_CustId;
    public $CustId;
    public $UId;
    public $Access_token;
    public $AllowEmail;
    public $AllowPost;
    public $Enabled;
}