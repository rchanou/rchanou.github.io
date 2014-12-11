<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Utility\Objects;

class SqlBuilder {

    private function __construct() {} // prevent creation of "static" class

    private static function getRecordIdentity($record) {
        return self::mutateByRecordKeys($record, function($key, $keys) {
            return (!in_array($key, $keys));
        });
    }

    private static function stripRecordIdentity($record) {
        return self::mutateByRecordKeys($record, function($key, $keys) {
            return (in_array($key, $keys));
        });
    }

    private static function mutateByRecordKeys($record, $callback) {
        // return a copy of the record only containing everything BUT primary keys and their values
        $tempRecord = clone $record;
        $keys = $tempRecord::$key;
        if (!is_array($keys))
            $keys = array($keys);
        foreach($tempRecord as $key => $val) {
            if ($callback($key, $keys))
                $tempRecord->$key = null;
        }
        return $tempRecord;
    }

    private static function allKeysSet($record) {
        $keys = $record::$key;
        if (!is_array($keys))
            $keys = array($keys);
        foreach($keys as $key) {
            if (is_null($record->{$key}))
                return false;
        }
        return true;
    }

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
        $select = self::buildSelect($record);
        $identity = self::getRecordIdentity($record);
        $where = self::buildWhere($identity);
        $get['statement'] = ""
            ."\n" . $select['statement']
            ."\n" . $where['statement'];
        $get['values'] = $where['values'];
        return $get;
    }

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
        $valueCounter = 0; // switching to value counter, since a count is no longer sufficient with support for the IN keyword
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
                $param = ':p' . $valueCounter++;
                $where['columns'][] = array(
                      'left'        => $param
                    , 'operator'    => ' '. $comparator->operator . ' '
                    , 'right'       => $record::$tableAlias . '.' . $comparator->right
                    , 'connector'   => isset($connector) ? $connector . ' ' : null
                );
                $where['values'][$param] = $comparator->left; // alias it to protect against injection
            }
            else if (is_array($comparator->right) || !property_exists($record, $comparator->right)) {
                // right is a value or an array for IN
                // check for IS(?: NOT) NULL special case
                if ($comparator->right != 'NULL') { // convert to DB_NULL at some point -- make map do the conversion (?)
                    $tempColumn = array(
                        'left'        => $record::$tableAlias . '.' . $comparator->left
                        , 'operator'    => ' '. $comparator->operator . ' '
                        , 'connector'   => isset($connector) ? $connector . ' ' : null
                    );
                    // if we allow "IN" statements, we can have arrays here
                    // and then each array item should be parameterized
                    if (is_array($comparator->right)) {
                        $tempParams = array();
                        foreach($comparator->right as $rightVal) {
                            $param = ':p' . $valueCounter++;
                            $where['values'][$param] = $rightVal;
                            $tempParams[] = $param;
                        }
                        $tempColumn['right'] = '(' . implode(', ', $tempParams) . ')';
                    }
                    else {
                        $param = ':p' . $valueCounter++;
                        $where['values'][$param] = $comparator->right;
                        $tempColumn['right'] = $param;
                    }
                    $where['columns'][] = $tempColumn;
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
        // if $where['columns'] is not set, throw an error?
        // shouldn't ever happen unless we get a server error,
        // or something sneaks by the logic classes.
        if (empty($where['columns']))
            throw new \CSException("Attempted to build a where clause for " . $record::$table . " without any columns!");

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

        $identity = self::getRecordIdentity($record);
        if (Objects::isEmpty($identity))
            throw new \CSException("Attempted to update using a record which did not contain any primary keys!");
        if (!self::allKeysSet($identity)) // is empty is not sufficient -- what if we have part of a primary key?
            throw new \CSException("Attempted to update using a record which did not contain a full set of primary keys! Received: " . print_r($record, true)); // possible security risk?


        $where = self::buildWhere($identity);
        $record = self::stripRecordIdentity($record); // more testing required
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
        $identity = self::getRecordIdentity($record);
        if (!self::allKeysSet($identity)) // is empty is not sufficient -- what if we have part of a primary key?
            throw new \CSException("Attempted to delete using a record which did not contain a full set of primary keys! Received: " . print_r($record, true)); // possible security risk?
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
        $identity = self::getRecordIdentity($record);
        if (!self::allKeysSet($identity)) // is empty is not sufficient -- what if we have part of a primary key?
            throw new \CSException("Attempted to check for existence of a record which did not contain a full set of primary keys! Received: " . print_r($record, true)); // possible security risk?
        $where = self::buildWhere($identity);
        $exists = array();
        $exists['statement'] = ''
            ."\nSELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT TOP 1 " . $record::$tableAlias . ".*"
            ."\n        FROM " . $record::$table . " " . $record::$tableAlias
            ."\n        " . $where['statement'] // $record::$tableAlias . "." . $record::$key . " = :" . $record::$key
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS [Exists]";
        $exists['values'] = $where['values'];
        return $exists;
    }
}