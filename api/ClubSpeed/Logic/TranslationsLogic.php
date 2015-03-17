<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums as Enums; // use statement needs to be below namespace

/**
 * The business logic class
 * for ClubSpeed screen templates.
 */
class TranslationsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the TranslationsLogic class.
     *
     * The TranslationsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->translations;
    }

    public final function create($params = array()) {
        $interface =& $this->interface;
        return parent::_create($params, function($translation) use (&$interface) {
            $find = $interface->match(array(
                  'Namespace' => $translation->Namespace
                , 'Name'      => $translation->Name
                , 'Culture'   => $translation->Culture
            ));
            if (!empty($find)) {
                throw new \RecordAlreadyExistsException(
                    'Translation create found a record which already exists!'
                    . ' Received Namespace: '   . $translation->Namespace
                    . ' and Name: '             . $translation->Name
                    . ' and Culture: '          . $translation->Culture
                );
            }
            if (empty($translation->DefaultValue))
                $translation->DefaultValue = $translation->Value; // or the other way around?

            return $translation; // use reference instead of return?
        });
    }

    public final function batchCreate($mapped = array()) {
        $new = array();
        $existing = array();
        foreach($mapped as $key => $data) {
            $translation = $this->interface->dummy($data);
            // check to see if the resource set already exists,
            // by combination of ResourceSetName and ResourceName
            $find = $this->interface->match(array(
                  'Namespace' => $translation->Namespace
                , 'Name'      => $translation->Name
                , 'Culture'   => $translation->Culture
            ));
            if (empty($find)) {
                // if the resource set does not already exist, 
                // throw it on $new, to be used with the internal batchCreate method
                $new[$key] = $translation;
            }
            else {
                // if the resource set does already exist, consider this a business logic error
                $existing[$key] = array(
                    "error" => 'Translation create found a record which already exists! Received Namespace: ' . $translation->Namespace . ' and Name: ' . $translation->Name . ' and Culture: ' . ($translation->Culture === Enums::DB_NULL ? 'null' : $translation->Culture)
                );
            }
        }
        $return = array();
        if (!empty($new)) {
            $batch = $this->interface->batchCreate($new);
            $pk = $this->interface->keys[0];
            foreach($batch as $key => $val) {
                if (is_int($val))
                    $new[$key] = array($pk => $val);
                else // if we receive anything other than an int, assume it is an error and needs to be sent back as-is
                    $new[$key] = $val;
            }
        }
        $return = $new + $existing;
        ksort($return);
        return $return;
    }

    public final function batchUpdate($mapped = array()) {
        $errors = array();
        foreach($mapped as $key => $data) {
            try {
                $record = $this->interface->dummy($data);
                $this->update($record->{$this->interface->keys[0]}, $record);
            }
            catch(\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        if (!empty($errors))
            return $errors;
    }

    public function update(/* $id, $params = array() */) {
        $args = func_get_args();
        $logic =& $this->logic;
        $interface =& $this->interface;
        $closure = function($old, $new) use (&$interface) {
            if (
                    $old->Namespace != $new->Namespace
                ||  $old->Name      != $new->Name
                ||  $old->Culture   != $new->Culture
            ) {
                // try to capture indexing errors before we make it to the insert
                $find = $interface->match(array(
                      'Namespace' => $new->Namespace
                    , 'Name'      => $new->Name
                    , 'Culture'   => $new->Culture
                ));
                if (!empty($find)) {
                    throw new \RecordAlreadyExistsException(
                        'Translation update found a record which already exists!'
                        . ' Received Namespace: '   . $new->Namespace
                        . ' and Name: '             . $new->Name
                        . ' and Culture: '          . $new->Culture
                    );
                }
            }
            return $new;
        };
        array_push($args, $closure);
        return call_user_func_array(array("parent", "update"), $args);
    }
}