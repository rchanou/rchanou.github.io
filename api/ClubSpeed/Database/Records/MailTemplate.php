<?php

namespace ClubSpeed\Database\Records;

class MailTemplate extends BaseRecord {

    public static $table      = 'dbo.MailTemplate';
    public static $tableAlias = 'mt';
    public static $key        = 'EmailTemplateID';
    
    public $EmailTemplateID;
    public $Text;
    public $Subject;

    public function __construct($data = array()) {
        $this->load($data);
    }

    public function load($data = array()) {
        if (isset($data)) {
            if (is_array($data)) {
                if (!empty($data)) {
                    if (isset($data['EmailTemplateID']))    $this->EmailTemplateID = \ClubSpeed\Utility\Convert::toNumber   ($data['EmailTemplateID']);
                    if (isset($data['Text']))               $this->Text            = \ClubSpeed\Utility\Convert::toString   ($data['Text']);
                    if (isset($data['Subject']))            $this->Subject         = \ClubSpeed\Utility\Convert::toString   ($data['Subject']);
                }
            }
            else {
                $this->{self::$key} = \ClubSpeed\Utility\Convert::toNumber($data);
            }
        }
    }

    public function validate($type) {
        // todo
    }
}