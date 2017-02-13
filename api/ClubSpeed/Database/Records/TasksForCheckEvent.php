<?php

namespace ClubSpeed\Database\Records;

class TasksForCheckEvent extends BaseRecord {
    protected static $_definition;

    public $TaskID;
    public $Deleted;
    public $Seq;
    public $TaskDescription;
    public $TaskName;
}