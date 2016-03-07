<?php

namespace ClubSpeed\Database\Records;

class ProductClasses extends BaseRecord {
    protected static $_definition;

    public $ProductClassID;
    public $Description;
    public $Deleted;
    public $ExportName;
}
