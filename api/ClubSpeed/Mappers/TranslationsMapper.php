<?php

namespace ClubSpeed\Mappers;

class TranslationsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'translations';
        $this->register(array(
              'TranslationsID' => ''
            , 'Namespace'      => ''
            , 'Name'           => ''
            , 'Culture'        => ''
            , 'DefaultValue'   => ''
            , 'Value'          => ''
            , 'Description'    => ''
            , 'Created'        => ''
        ));
    }

    protected final function compress($data = array()) {
        if (!isset($data) || is_null($data))
            return null;
        $table = $this->namespace ?: "records";
        if ($this->is_assoc($data)) { // for the id => {id} arrays coming from create calls. this seems hacky -- consider another option
            foreach($data as $key => $value) {
                $compressed[$this->map('client', $key)] = $value;
            }
        }
        else {
            $compressed = array(
                $table => array()
            );
            $inner =& $compressed[$table];
            if (isset($data) && !is_array($data))
                $data = array($data);
            if (!empty($data)) {
                foreach($data as $record) {
                    if (!empty($record)) {
                        if (is_array($record) && isset($record['error'])) // for batchCreate methods. consider patching into base class
                            $inner[] = $record;
                        else {
                            $inner[] = $this->map('client', $record);
                        }
                    }
                } 
            }
        }
        return $compressed;
    }
}