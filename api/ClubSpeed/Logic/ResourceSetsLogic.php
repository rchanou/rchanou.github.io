<?php

namespace ClubSpeed\Logic;
use ClubSpeed\Enums\Enums as Enums; // use statement needs to be below namespace

/**
 * The business logic class
 * for ClubSpeed screen templates.
 */
class ResourceSetsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ResourceSetsLogic class.
     *
     * The ResourceSetsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->resourceSets;
    }

    public final function create($params = array()) {
        $interface =& $this->interface;
        return parent::_create($params, function($resourceSet) use (&$interface) {
            $find = $interface->match(array(
                'ResourceSetName'   => $resourceSet->ResourceSetName
                , 'ResourceName'    => $resourceSet->ResourceName
                , 'Culture'         => $resourceSet->Culture
            ));
            if (!empty($find))
                throw new \RecordAlreadyExistsException('ResourceSets create found a record which already exists! '
                    . ' Received ResourceSetName: ' . $resourceSet->ResourceSetName
                    . ' and Resourcename: '         . $resourceSet->ResourceName
                    . ' and Culture: '              . $resourceSet->Culture
                );

            return $resourceSet; // use reference instead of return?
        });
    }

    public final function batchCreate($mapped = array()) {
        $new = array();
        $existing = array();
        foreach($mapped['batch'] as $key => $map) {
            // $map = $this->map('server', $val);
            if (!isset($map['Culture']))
                $map['Culture'] = Enums::DB_NULL; // for searching purposes, nulls should be matched(!!!)

            // check to see if the resource set already exists,
            // by combination of ResourceSetName and ResourceName
            $find = $this->interface->match(
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
                    "error" => 'ResourceSets create found a record which already exists! Received ResourceSetName: ' . $map['ResourceSetName'] . ' and ResourceName: ' . $map['ResourceName'] . ' and Culture: ' . ($map['Culture'] === Enums::DB_NULL ? 'null' : $map['Culture'])
                );
            }
        }
        $return = array();
        if (!empty($new)) {

            // run the batch create with any non-existing resource sets
            $batch = $this->interface->batchCreate($new);

            $pk = $this->interface->key;
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

    public final function getNamespace($params) {
        // $params = $mapped->params;
        // if culture is en-US or null, set it to DB_NULL to match the database data
        if (is_null($params['ResourceSetName']) || empty($params['ResourceSetName']))
            throw new \RequiredArgumentMissingException('ResourceSetsLogic getNamespace received a null or empty ResourceSetName!');
        
        // $language = $params['Culture']; // don't think we're even using culture for search anymore.

        return $this->interface->match(
            array(
                'ResourceSetName' => $params['ResourceSetName']
            )
        );
    }

    public final function compress($data = array(), $limit = array()) {
        // $this->limit('client', $limit);
        $compressed = array(
            $this->namespace => array()
        );
        $inner =& $compressed[$this->namespace];
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