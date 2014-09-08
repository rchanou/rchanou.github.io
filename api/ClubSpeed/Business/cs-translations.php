<?php

namespace ClubSpeed\Business;

require_once(__DIR__.'../../Utility/Convert.php');

/**
 * The business logic class
 * for ClubSpeed screen templates.
 */
class CSTranslations {

    /**
     * A reference to the parent ClubSpeed logic container.
     */
    private $logic;

    /**
     * A reference to the injected ClubSpeed database class.
     */
    private $db;

    /**
     * Constructs a new instance of the CSReservations class.
     *
     * The CSReservations constructor requires an instantiated CSDatabase class for injection,
     * as well as a reference to the CSLogic container where this class will be stored.
     * The parent is passed for communication across business logic classes.
     *
     * @param CSLogic $CSLogic The parent CSLogic container.
     * @param CSConnection $CSConnection The CSConnection class to inject.
     */
    public function __construct(&$CSLogic, &$CSDatabase) {
        $this->logic = $CSLogic;
        $this->db = $CSDatabase;
    }

    /**
     * Document: TODO
     */
    public final function create($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->resourceSets->map('server', $params);
        $record = $this->db->resourceSets->dummy();
        $record->load($mapped);
        $record->validate('insert'); // validate early before attempting the find with potentially missing columns?
        $find = $this->db->resourceSets->find(array(
            'ResourceSetName'   => $record->ResourceSetName
            , 'ResourceName'    => $record->ResourceName
            , 'Culture'         => $record->Culture
        ));
        if (!empty($find))
            throw new \RecordAlreadyExistsException('Translation create found a record which already exists! Received ResourceSetName: ' . $mapped['ResourceSetName'] . ' and Resourcename: ' . $mapped['ResourceName']);
        $resourceId = $this->db->resourceSets->create($mapped);
        $create = $this->db->resourceSets->map(
            'client'
            , array(
                $this->db->resourceSets->key => $resourceId
            )
        );
        return $create;
    }

    public final function batchCreate($params = array()) {

        // TODO: clean up, once the structure has been fully defined

        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $new = array();
        $existing = array();

        foreach($params['batch'] as $key => $val) {
            $map = $this->db->resourceSets->map('server', $val);
            if (!isset($map['Culture']))
                $map['Culture'] = \CSEnums::DB_NULL; // for searching purposes, nulls should be matched(!!!)

            // check to see if the resource set already exists,
            // by combination of ResourceSetName and ResourceName
            $find = $this->db->resourceSets->find(
                array(
                    'ResourceSetName'   => @$map['ResourceSetName'] // do it this way with the @ symbol?
                    , 'ResourceName'    => @$map['ResourceName'] // or make dummy records and validate each insert that way?
                    , 'Culture'         => @$map['Culture']
                )
            );

            if (empty($find)) {
                // if the resource set does not already exist, 
                // throw it on $new, to be used with the internal batchCreate method
                $new[$key] = $map;
            }
            else {
                // if the resource set does already exist,
                // consider this a business logic error
                $existing[$key] = array(
                    "error" => 'Translation create found a record which already exists! Received ResourceSetName: ' . $map['ResourceSetName'] . ' and ResourceName: ' . $map['ResourceName'] . ' and Culture: ' . ($map['Culture'] === \CSEnums::DB_NULL ? 'null' : $map['Culture'])
                );
            }
        }

        $return = array();
        if (!empty($new)) {

            // run the batch create with any non-existing resource sets
            $batch = $this->db->resourceSets->batchCreate($new);

            // collect the primary key name for the table, map it to the client value
            $pk = $this->db->resourceSets->map('client', $this->db->resourceSets->key);
            foreach($batch as $key => $val) {

                if (is_int($val)) {
                    // if we receive an int, assume it is the primary key and a successful create
                    $new[$key] = array($pk => $val);
                }
                else {
                    // if we receive anything other than an int, assume it is an error and needs to be sent back as-is
                    $new[$key] = $val;
                }
            }
        }
        $return = $new + $existing;
        ksort($return);
        return $return;
    }

    public final function all() {
        $all = $this->db->resourceSets->all();
        $compressed = $this->db->resourceSets->compress($all);
        return $compressed;
    }

    public final function get($resourceId) {
        $get = $this->db->resourceSets->get($resourceId);
        $compressed = $this->db->resourceSets->compress($get);
        return $compressed;
    }

    public final function find($params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $mapped = $this->db->resourceSets->map('server', $params);
        $find = $this->db->resourceSets->find($mapped);
        $compressed = $this->db->resourceSets->compress($find);
        return $compressed;
    }

    /**
     * Document: TODO
     */
    public final function update($resourceId, $params = array()) {
        $params = \ClubSpeed\Utility\Params::nonReservedData($params);
        $resourceSets = $this->db->resourceSets->get($resourceId);
        if (is_null($resourceSets))
            throw new \RecordNotFoundException("Attempted to update a non-existent resource set! Received resourceId: " . $resourceId);
        $resourceSets = $this->db->resourceSets->blank();
        $resourceSets->load($resourceId);
        $mapped = $this->db->resourceSets->map('server', $params);
        $resourceSets->load($mapped);
        return $this->db->resourceSets->update($resourceSets);
    }

    /**
     * Document: TODO
     */
    public final function delete($resourceId) {
        return $this->db->resourceSets->delete($resourceId);
    }

    private function &array_first(&$arr, $predicate) {
        $return = null;
        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                if (call_user_func($predicate, $arr[$i])) {
                    return $arr[$i];
                }
            }
        }
        return $return;
    }

    public final function getNamespace($namespace, $language = null) {
        $sw = $GLOBALS['sw'];
        $completeList = $this->db->resourceSets->find(
            array(
                'ResourceSetName' => $namespace
            )
        );
        $merged = array();
        foreach($completeList as $key => $complete) {
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
        $compressed = $this->db->resourceSets->compress($merged);
        return $compressed;
    }
}