<?php

namespace ClubSpeed\Database\Records;

class MailTemplate extends BaseRecord {
    protected static $_definition;
    
    public $EmailTemplateID;
    public $Text;
    public $Subject;
}