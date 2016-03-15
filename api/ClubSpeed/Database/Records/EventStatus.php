<?php

namespace ClubSpeed\Database\Records;

class EventStatus extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $ColorID;
    public $Seq;
    public $Status;
}