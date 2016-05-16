<?php

namespace ClubSpeed\Database\Records;

class GiftCardBalance_V extends BaseRecord {
    protected static $_definition;

    public $CustID;
    public $CrdID;
    public $IsGiftCard;
    public $Money;
    public $Points;
}