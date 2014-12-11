<?php

namespace ClubSpeed\Mappers;

class ResourceSetsMapper extends BaseMapper {

    public function __construct() {
        parent::__construct();
        $this->namespace = 'translation';
        $this->register(array(
            'ResourceID'        => 'resourceId'
            , 'ResourceSetName' => 'namespace'
            , 'Culture'         => 'language'
            , 'ResourceName'    => 'name'
            , 'ResourceValue'   => 'value'
            , 'ResourceType'    => 'type'
            , 'ResourceComment' => 'comment'
        ));
    }

    protected final function compress($data = array()) {
        if (!isset($data))
            return null;

        if ($this->is_assoc($data)) { // for the id => {id} arrays coming from create calls. this seems hacky -- consider another option
            foreach($data as $key => $value) {
                $compressed[$this->map('client', $key)] = $value;
            }
        }
        else {
            $merged = array();
            foreach($data as $key => $complete) {
                if ($this->is_assoc($complete)) { // for the id => {id} arrays coming from create calls. this seems hacky -- consider another option
                    return null; // hacky, but this is a batchCreate -- just break early for now. we can do something useful with this later
                }
                else {
                    if (is_null($complete->Culture)) { // defaults
                        $current = &$this->array_first($merged, function($x) use ($complete) {
                            return (
                                    $x->ResourceSetName === $complete->ResourceSetName
                                &&  $x->ResourceName    === $complete->ResourceName
                            );
                        });
                        if (is_null($current))
                            $merged[] = $complete;
                    }
                    else if ($complete->Culture === 'en-US') { // overrides
                        $current = &$this->array_first($merged, function($x) use ($complete) {
                            return (
                                    $x->ResourceSetName === $complete->ResourceSetName
                                &&  $x->ResourceName    === $complete->ResourceName
                            );
                        });
                        if (is_null($current))
                            $merged[] = $complete;
                        else
                            $current = $complete; // override 'null' cultures with 'en-US' cultures, if they exist
                    }
                    else
                        $merged[] = $complete; // if the culture is not null or en-US, assume we are taking it
                }
            }
            $compressed = array(
                $this->namespace => array()
            );
            $inner =& $compressed[$this->namespace];
            if (isset($merged)) {
                if (!is_array($merged))
                    $merged = array($merged);
                foreach($merged as $record) {
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
        }
        return $compressed;
    }
}