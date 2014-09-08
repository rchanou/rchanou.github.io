<?php

namespace ClubSpeed\Database\Collections;

require_once(__DIR__.'/DbCollection.php');
require_once(__DIR__.'/../Records/ResourceSets.php');

class DbResourceSets extends DbCollection {

    public $key;

    public function __construct($db) {
        parent::__construct($db);
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\ResourceSets');
        $this->dbToJson = array(
             "ResourceID"       => "resourceId"
            , "ResourceSetName" => "namespace"
            , "Culture"         => "language"
            , "ResourceName"    => "name"
            , "ResourceValue"   => "value"
            , "ResourceType"    => "type"
            , "ResourceComment" => "comment"
        );
        parent::secondaryInit();
    }

    public function compress($data = array()) {
        $table = 'translations';
        $compressed = array(
            $table => array()
        );
        $inner =& $compressed[$table];
        if (isset($data)) {
            if (!is_array($data))
                $data = array($data);
            foreach($data as $record) {
                if (!empty($record)) {
                    if (is_null($record->Culture))
                        $record->Culture = 'en-US';
                    if (!isset($inner[$record->Culture]) || !is_array($inner[$record->Culture])) {
                        $inner[$record->Culture] = array();
                    }
                    $inner[$record->Culture][] = $this->map('client', $record);
                }
            }
        }
        return $compressed;
    }
}