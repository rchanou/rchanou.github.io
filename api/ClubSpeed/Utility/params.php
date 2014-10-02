<?php

namespace ClubSpeed\Utility;
use ClubSpeed\Enums\Enums as Enums;

/**
 * A utility class containing helper methods for dynamic parameters.
 */
class Params {

    private static $reserved = array(
        'key'
        , 'debug'
        , 'select'
        , 'XDEBUG_PROFILE'
    );

    private static $special = array(
        'filter'
    );

    /**
     * Dummy constructor to prevent any initialization of the Validate Class
     */
    private function __construct() {} // prevent any initialization of this class

    public static function hasNonReservedData($data) {
        if (is_null($data) || empty($data))
            return false;
        foreach($data as $key => $val) {
            // if (!in_array($key, self::$reserved) && !in_array($key, self::$special) && isset($val)) {
            if (!in_array($key, self::$reserved) && isset($val)) {
                return true;
            }
        }
        return false;
    }

    public static function nonReservedData($data) {
        if (is_null($data) || empty($data))
            return $data;
        foreach(self::$reserved as $val) {
            if (isset($data[$val]))
                unset($data[$val]);
        }
        return $data;
    }

    public static function isFilter($data) {
        if (is_null($data) || empty($data))
            return false;
        if (isset($data['filter']))
            return true;
        return false;
    }

    // for use with params coming from the api
    // cleanParams (below) should be deprecated 
    // at some point for the newer structure
    public static function clean($data, $limit = array()) {
        $return = array(
            'params' => array()
        );
        if (is_null($data) || empty($data)) {
            return $return; // leave early?
        }

        foreach($data as $key => $val) {
            // move the limit for insert/update to here? then we have to use json styled-names, instead of database styled-names
            if (
                !in_array($key, self::$reserved)
                && !in_array($key, self::$special)
                && (!empty($limit) ? in_array($key, $limit) : true)
            ) {
                $return['params'][$key] = $val;
            }
        }
        if (isset($data['select'])) {
            foreach(explode(',', $data['select']) as $key => $val) {
                $return['select'][$key] = trim($val);
            }
        }
        if (isset($data['filter']))
            $return['filter'] = $data['filter']; // keep as string for now?

        return $return;
    }

    // deprecated (!!!)
    // public static function map($map = array(), $params = array()) {
    //     $mapped = null;
    //     if (is_array($params) || is_object($params)) {
    //         $mapped = array();
    //         foreach($params as $key => $val) {
    //             if (isset($map[$key]))
    //                 $mapped[$map[$key]] = $val;
    //         }
    //     }
    //     else {
    //         // expected a single name, not multiple params as an associative array -- rename parameter?
    //         switch($type) {
    //             case 'client':
    //                 $mapped = $map[$params];
    //                 break;
    //             case 'server':
    //                 $mapped = $map[$params];
    //                 break;
    //         }
    //     }
    //     return $mapped;
    // }

    public static function buildInsert($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
            throw new \InvalidArgumentException("Attempted to build insert statement with a non BaseRecord! Received: " . $record);
        if (\ClubSpeed\Utility\Objects::isEmpty($record))
            throw new \InvalidArgumentValueException("Attempted to build insert statement with an empty BaseRecord!");
        $insert = array(
              'names'   => array()
            , 'values'  => array()
            , 'aliases' => array()
        );
        foreach($record as $name => $value) {
            if (isset($value) && $value !== Enums::DB_NULL) {
                $insert['names'][]      = $name;
                $insert['values'][]     = $value;
                $insert['aliases'][]    = ":" . $name;
            }
        }
        $insert['statement'] = ""
            ."\nINSERT INTO " . $record::$table . " ("
            ."\n    " . implode(", ", $insert['names'])
            ."\n)"
            ."\nVALUES ("
            ."\n    " . implode(", ", $insert['aliases'])
            ."\n)"
            ;
        return $insert;
    }

    public static function buildSelect($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
            throw new \InvalidArgumentException("Attempted to select from a non BaseRecord! Received: " . $record);
        $select = array(
            'names'         => array()
            , 'table'       => $record::$table
            , 'tableAlias'  => $record::$tableAlias
        );
        foreach ($record as $name => $value) {
            if (isset($name)) { // probably not necessary
                $select['names'][] = $record::$tableAlias . '.' . $name;
            }
        }
        $select['names'][0] = '    ' . $select['names'][0]; // for readability (?)
        $select['statement'] = ""
            ."\nSELECT"
            ."\n" . implode("\n    , ", $select['names'])
            ."\nFROM " . $select['table'] . ' AS ' . $select['tableAlias']
            ;
        return $select;
    }

    public static function buildGet($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
            throw new \InvalidArgumentException("Attempted to get from a non BaseRecord! Received: " . $record);
        $get = array(
            "key"       => $record::$key
            , "param"   => ":" . $record::$key
        );
        $get['values'][$get['param']] = $record->{$record::$key};
        $select = self::buildSelect($record);
        $get['statement'] = ""
            ."\n" . $select['statement']
            ."\nWHERE"
            ."\n    " . $record::$tableAlias . '.' . $get['key'] . " = " . $get['param']
            ;
        return $get;
    }

    // public static function buildMatch($record = array()) {
    //     // extension to convert a $record into the expected buildFind comparators,
    //     // then make an exact match on the provided record properties
    //     if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
    //         throw new \InvalidArgumentException("Attempted to get from a non BaseRecord! Received: " . $record);

    //     $comparators = new \ClubSpeed\Database\Helpers\GroupedComparator($record); // assumes direct matches for any set properties
    //     return self::buildFind($record, $comparators);
    // }

    public static function buildFind($definition, $groupedComparators = null) {
        if ($definition instanceof \ReflectionClass) {
            $record = $definition->newInstance();
        }
        else if ($definition instanceof \ClubSpeed\Database\Records\BaseRecord) {
            $record = $definition;
            if (is_null($groupedComparators))
                $groupedComparators = new \ClubSpeed\Database\Helpers\GroupedComparator($record);
        }
        else {
            // should throw an exception here, probably -- didn't receive a definition or a grouped comparator
            throw new \CSException("Params::buildFind() received a definition which was unable to be converted into a BaseRecord! Received: " . $definition);
        }
        $select = self::buildSelect($record);
        if (is_null($groupedComparators) || $groupedComparators->isEmpty()) {
            return $select; // just return the "all" select statement?
        }
        $find = array(
              'columns' => array()
            , 'values'  => array()
        );
        $where = self::buildWhere($record, $groupedComparators);
        $find['statement'] = ""
            ."\n" . $select['statement']
            ."\n" . $where['statement']
            ;
        $find['values'] = @$where['values'];
        return $find;
    }

    public static function buildWhere($record, $groupedComparators = null) {
        $where = array();
        if (is_null($groupedComparators))
            $groupedComparators = new \ClubSpeed\Database\Helpers\GroupedComparator($record); // build if null?
        $comparators = $groupedComparators->comparators;
        foreach($comparators as $key => $data) {
            $comparator = $data['comparator'];
            $connector = @$data['connector'];
            // at least one of left or right has to be a column, after filter validation
            // note: is this really the job of this function, or should this alias determination
            // and value vs column determination be moved to a validation method elsewhere (?)
            if (!property_exists($record, $comparator->left)) {
                // left is a value
                $param = ':p' . (count(@$where['values']) ?: 0);
                $where['columns'][] = array(
                      'left'        => $param
                    , 'operator'    => ' '. $comparator->operator . ' '
                    , 'right'       => $record::$tableAlias . '.' . $comparator->right
                    , 'connector'   => isset($connector) ? $connector . ' ' : null
                );
                $where['values'][$param] = $comparator->left; // alias it to protect against injection
            }
            else if (!property_exists($record, $comparator->right)) {
                // right is a value
                if ($comparator->right != 'NULL') { // convert to DB_NULL at some point -- make map do the conversion (?)
                    // check for IS(?: NOT) NULL special case
                    $param = ':p' . (count(@$where['values']) ?: 0);
                    $where['columns'][] = array(
                          'left'        => $record::$tableAlias . '.' . $comparator->left
                        , 'operator'    => ' '. $comparator->operator . ' '
                        , 'right'       => $param
                        , 'connector'   => isset($connector) ? $connector . ' ' : null
                    );
                    $where['values'][$param] = $comparator->right;
                }
                else {
                    // don't alias the right, if it is supposed to be a NULL comparison
                    // CAREFUL FOR INJECTION HERE -- more dangerous than the others
                    $where['columns'][] = array(
                          'left'        => $record::$tableAlias . '.' . $comparator->left
                        , 'operator'    => ' '. $comparator->operator . ' '
                        , 'right'       => $comparator->right
                        , 'connector'   => isset($connector) ? $connector . ' ' : null
                    );
                }
            }
            else {
                // both are columns (?)
                // handle later if necessary, this isn't likely to be used
                return null;
            }
        }
        foreach($where['columns'] as $key => $val) {
            $where['columns'][$key] = "\n    "
                . @$val['connector'] . ''
                . $val['left']
                . $val['operator']
                . $val['right']
                ;
        }
        $where['statement'] = "\nWHERE" . implode("", $where['columns']);
        return $where;
    }

    public static function buildUpdate($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
            throw new \InvalidArgumentException("Attempted to update using a non BaseRecord! Received: " . $record);

        // get a copy of the record
        // clean all parameters but the id
        // check this for efficiency (!!!)
        $tempRecord = clone $record;
        foreach($tempRecord as $key => $val) {
            if ($key !== $tempRecord::$key) {
                $tempRecord->$key = null;
            }
        }
        $where = self::buildWhere($tempRecord);

        // chop the id out of the non-cloned record
        $id = $record->{$record::$key};
        $record->{$record::$key} = null;

        $update = array(
              'values'  => array()
            , 'columns'  => array()
        );
        foreach($record as $name => $value) {
            if (isset($value)) {
                $update['columns'][] = array(
                      'name' => $name
                    , 'alias' => ":" . $name
                );
                $update['values'][":" . $name] = ($value === Enums::DB_NULL ? NULL : $value);
            }
        }
        $tablePrefix = $record::$tableAlias . '.';
        foreach($update['columns'] as $key => $val) {
            $update['columns'][$key] = "\n    "
                . $tablePrefix
                . $val['name']
                . ' = '
                . $val['alias']
                ;
        }
        $update['statement'] = ""
            ."\nUPDATE " . $record::$tableAlias
            ."\nSET"
            . implode(", ", $update['columns'])
            ."\nFROM " . $record::$table . ' AS ' . $record::$tableAlias
            . $where['statement']
            ;
        $update['values'] = array_merge($update['values'], $where['values']);
        return $update;
    }

    public static function buildDelete($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
            throw new \InvalidArgumentException("Attempted to delete from a non BaseRecord! Received: " . $record);
        if (is_null($record->{$record::$key}))
            throw new \InvalidArgumentException("Attempted to delete a BaseRecord without providing an id! Received: " . $record);
        
        $where = self::buildWhere($record);
        $delete['statement'] = ""
            ."\nDELETE " . $record::$tableAlias
            ."\nFROM " . $record::$table . ' AS ' . $record::$tableAlias
            ."\n" . $where['statement']
            ;
        $delete['values'] = $where['values'];
        return $delete;
    }

    public static function buildExists($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\BaseRecord)
            throw new \InvalidArgumentException("Attempted to check existence from a non BaseRecord! Received: " . $record);
        if (is_null($record->{$record::$key}))
            throw new \InvalidArgumentException("Attempted to check existence of a BaseRecord without providing an id! Received: " . $record);
        
        $exists = array();
        $exists['statement'] = ''
            ."\nSELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT " . $record::$tableAlias . ".*"
            ."\n        FROM " . $record::$table . " " . $record::$tableAlias
            ."\n        WHERE " . $record::$tableAlias . "." . $record::$key . " = :" . $record::$key
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS [Exists]";
        $exists['values'] = array(
            ":" . $record::$key => $record->{$record::$key}
        );
        return $exists;
    }

    /**
     * Dynamically skims a list of reqired and allowed parameters out of a list of existing parameters,
     * throwing errors whenever required parameters are missing from the original list.
     *
     * @param string[int]   $requiredParams (optional)  A list of the required parameters.
     * @param string[int]   $allowedParams  (optional)  A list of the allowed parameters.
     * @param mixed[string] $currentParams              The set of parameters to be cleaned.
     * @return mixed[string] The set of cleaned parameters.
     * @throws RequiredArgumentMissingException if there is a required parameter which could not be found in the current parameters.
     */
    public static function cleanParams($requiredParams = array(), $allowedParams = array(), $currentParams = array()) {
        $paramsCleaned = array();
        foreach($requiredParams as $requiredParam) {
            if (!isset($currentParams[$requiredParam]) || $currentParams[$requiredParam] === "") {
                throw new \RequiredArgumentMissingException("Required parameter $requiredParam was missing!");
            }
            $paramsCleaned[$requiredParam] = $currentParams[$requiredParam];
        }
        foreach($allowedParams as $allowedParam) {
            if (isset($currentParams[$allowedParam])) {
                $paramsCleaned[$allowedParam] = $currentParams[$allowedParam];
            }
        }
        return $paramsCleaned;
    }
}