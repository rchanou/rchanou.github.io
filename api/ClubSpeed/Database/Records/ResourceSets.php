<?php

namespace ClubSpeed\Database\Records;

class ResourceSets extends BaseRecord {
    protected static $_definition;

    public $ResourceID;
    public $ResourceSetName;
    public $Culture;
    public $ResourceName;
    public $ResourceValue;
    public $ResourceType;
    public $ResourceComment;
}