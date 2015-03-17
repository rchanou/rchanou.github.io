<?php

namespace ClubSpeed\Database\Records;

class GiftCardTransactions_V extends BaseRecord {
    protected static $_definition;

    // this is problematic, as there's no real primary key.
    // and if there's no real primary key, we can't join the partition
    // back to the original query properly for limits / orders.

    public $CrdID;
    public $Money;
    public $Points;
    public $Date;
    public $Notes;
}