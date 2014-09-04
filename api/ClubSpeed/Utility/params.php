<?php

namespace ClubSpeed\Utility;

/**
 * A utility class containing helper methods for dynamic parameters.
 */
class Params {

    private static $reserved = array(
        'key'
        , 'debug'
    );

    /**
     * Dummy constructor to prevent any initialization of the Validate Class
     */
    private function __construct() {} // prevent any initialization of this class

    private static function objIsEmpty($obj) {
        // this really doesn't belong in this class - move eventually
        foreach($obj as $val) {
            if (isset($val))
                return false;
        }
        return true;
    }

    public static function hasNonReservedData($data) {
        if (is_null($data) || empty($data))
            return false;
        foreach($data as $key => $val) {
            if (in_array($key, self::$reserved) === false && isset($val)) {
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

    public static function buildInsert($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\DbRecord)
            throw new \InvalidArgumentException("Attempted to get all of a non DbRecord! Received: " . $record);

        $insert = array(
              'names'   => array()
            , 'values'  => array()
            , 'aliases' => array()
        );
        foreach($record as $name => $value) {
            if (isset($value) && $value !== \CSEnums::DB_NULL) {
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
        if (!$record instanceof \ClubSpeed\Database\Records\DbRecord)
            throw new \InvalidArgumentException("Attempted to select from a non DbRecord! Received: " . $record);
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
        if (!$record instanceof \ClubSpeed\Database\Records\DbRecord)
            throw new \InvalidArgumentException("Attempted to get from a non DbRecord! Received: " . $record);
        $get = array(
            "key"       => $record::$key
            , "alias"   => ":" . $record::$key
        );
        $get['values'][$get['alias']] = $record->{$record::$key};

        $select = self::buildSelect($record);
        $get['statement'] = ""
            ."\n" . $select['statement']
            ."\nWHERE"
            ."\n    " . $record::$tableAlias . '.' . $get['key'] . " = " . $get['alias']
            ;
        return $get;
    }

    public static function buildFind($record = array(), $connector = "AND") {
        if (!$record instanceof \ClubSpeed\Database\Records\DbRecord)
            throw new \InvalidArgumentException("Attempted to find from a non DbRecord! Received: " . $record);
        $find = array(
              'columns' => array()
            , 'values'  => array()
        );
        $select = self::buildSelect($record);
        if (self::objIsEmpty($record)) {
            // we will be unable to build a true where statement
            // just return the select statement at this point (?)
            // the result may be misleading if we just return all objects
            // seems to be collect all statements -- maybe an error is best (?)
            return $select;
        }
        $where = self::buildWhere($record);
        $find['statement'] = ""
            ."\n" . $select['statement']
            ."\n" . $where['statement']
            ;
        $find['values'] = @$where['values'];
        return $find;
    }

    public static function buildWhere($record = array(), $connector = "AND") {
        // move on to build the where statement, if possible
        foreach($record as $name => $value) {
            if (isset($value)) {
                if ($value === \CSEnums::DB_NULL) {
                    $where['columns'][] = array(
                        'name'          => $name
                        , 'comparator'  => ' IS '
                        , 'alias'       => 'NULL'
                    );
                }
                else {
                    $where['columns'][] = array(
                          'name'        => $name
                        , 'value'       => $value
                        , 'alias'       =>  ":" . $name
                        , 'comparator'  => " = "
                    );
                    $where['values'][":" . $name] = $value;
                }
            }
        }
        foreach($where['columns'] as $key => $val) {
            $where['columns'][$key] = "\n    "
                . $record::$tableAlias . '.'
                . $val['name']
                . $val['comparator']
                . $val['alias']
                ;
        }
        $where['statement'] = "\nWHERE" . implode(" " . $connector . " ", $where['columns']);
        return $where;
    }

    public static function buildUpdate($record = array()) {
        if (!$record instanceof \ClubSpeed\Database\Records\DbRecord)
            throw new \InvalidArgumentException("Attempted to update using a non DbRecord! Received: " . $record);

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
                $update['values'][":" . $name] = ($value === \CSEnums::DB_NULL ? NULL : $value);
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
        if (!$record instanceof \ClubSpeed\Database\Records\DbRecord)
            throw new \InvalidArgumentException("Attempted to delete from a non DbRecord! Received: " . $record);
        if (is_null($record->{$record::$key}))
            // will this work with php 5.3? if not, use a temp variable to get the static property name
            throw new \InvalidArgumentException("Attempted to delete a DbRecord without providing an id! Received: " . $record);
        
        $where = self::buildWhere($record);
        $delete['statement'] = ""
            ."\nDELETE " . $record::$tableAlias
            ."\nFROM " . $record::$table . ' AS ' . $record::$tableAlias
            ."\n" . $where['statement']
            ;
        $delete['values'] = $where['values'];
        return $delete;
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
            if (!isset($currentParams[$requiredParam]) || empty($currentParams[$requiredParam])) {
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