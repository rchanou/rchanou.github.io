<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Enums\Enums;

class SqlBuilder {

    private function __construct() {} // prevent creation of "static" class

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
}