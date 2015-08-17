<?php

namespace ClubSpeed\Database\Records;

class EventContactStatusLookup extends BaseRecord {
    protected static $_definition;

    public $id;
    public $value;
    public $description;
    public $typeId;
    public $orderby;
    public $Deleted;
    public $Locked;
}