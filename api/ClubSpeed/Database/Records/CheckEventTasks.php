<?php

namespace ClubSpeed\Database\Records;

class CheckEventTasks extends BaseRecord {
    protected static $_definition;

    public $ID;
    public $CheckID;
    public $CompletedByUserID;
    public $DateCompleted;
    public $TaskID;
}