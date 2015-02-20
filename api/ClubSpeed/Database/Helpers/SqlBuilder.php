<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Database\Helpers\Comparator;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Utility\Arrays;
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

    public static function uow(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql statement with a non UnitOfWork! Received: " . print_r($uow, true));
        switch($uow->action) {
            case 'create':
                return self::buildUowCreate($uow);
            case 'all':
                return self::buildUowAll($uow);
            case 'get':
                return self::buildUowGet($uow);
            case 'count':
                return self::buildUowCount($uow);
            case 'exists':
                return self::buildUowExists($uow);
            case 'update':
                return self::buildUowUpdate($uow);
            case 'delete':
                return self::buildUowDelete($uow);
            default:
                throw new \InvalidArgumentValueException("SqlBuilder received a UnitOfWork with an unrecognized action! Received: " . $uow->action);
        }
    }

    

    public static function buildUowCreate(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql insert statement from a non UnitOfWork! Received: " . print_r($uow, true));
        if (!$uow->data instanceof \ClubSpeed\Database\Records\BaseRecord) // either throw exception, or try to make the base record ourselves
            $uow->data = $uow->table->newInstance($uow->data);
            // throw new \InvalidArgumentException("Attempted to build a sql create query with a non BaseRecord! Received: " . print_r($uow->data, true));
        if (Objects::isEmpty($uow->data))
            throw new \InvalidArgumentValueException("Attempted to build sql insert statement with an empty record! Received: " . print_r($uow->data, true));
        $create = array(
              'names'   => array()
            , 'values'  => array()
            , 'aliases' => array()
        );
        $table = $uow->table->getStaticPropertyValue('table');
        foreach($uow->data as $name => $value) {
            if (isset($value) && $value !== Enums::DB_NULL) {
                $create['names'][]      = $name;
                $create['values'][]     = $value;
                $create['aliases'][]    = ":" . $name;
            }
        }
        $create['statement'] = ""
            ."\nINSERT INTO " . $table . " ("
            ."\n      " . implode("\n    , ", $create['names'])
            ."\n)"
            ."\nVALUES ("
            ."\n      " . implode("\n    , ", $create['aliases'])
            ."\n)"
            ;
        return $create;
    }

    public static function buildUowAll(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql all query from a non UnitOfWork! Received: " . $uow);
        $all = array(
              'statement'   => ''
            , 'values'      => array()
        );
        $key   = $uow->table->getStaticPropertyValue('key');
        $table = $uow->table->getStaticPropertyValue('table');
        $alias = $uow->table->getStaticPropertyValue('tableAlias');

        // multi-key shenanigans. arguably an anti-pattern, but we have to support it.
        if (!is_array($key))
            $key = array($key);

        // the order portion of UnitOfWork is specific to 'all' actions.
        $order = array(); 
        if (!is_null($uow->order)) {
            foreach($uow->order as $column => $direction)
                $order[] = $alias . "." . $column . " " . $direction;
        }
        else {
            // no order is provided. just use the key ascending for the ROW_NUMBER().
            foreach($key as $column)
                $order[] = $alias . "." . $column . " ASC";
        }
        $order = implode(', ', $order);

        // more multi-key shenanigans (boo.)
        $paginationColumns = array();
        foreach($key as $column)
            $paginationColumns[] = $alias . "." . $column;

        // even more multi-key shenanigans!
        $paginationJoinOn = array();
        foreach($key as $column)
            $paginationJoinOn[] = "pg." . $column . " = " . $alias . "." . $column;

        $select = self::buildUowSelect($uow);
        $from = self::buildUowFrom($uow);
        $where = self::buildUowWhere($uow);
        
        $all['statement'] = ""
            ."\n;WITH pagination AS ("
            ."\n    SELECT"
            ."\n          " . implode("\n        , ", $paginationColumns)
            ."\n        , ROW_NUMBER() OVER (ORDER BY " . $order . ") AS [Rank]"
            ."\n    " . $from['statement']
            . (!empty($where['statement']) ? "\n    " . $where['statement'] : '')
            ."\n)"
            ."\n" . $select['statement']
            ."\n" . $from['statement']
            ."\nINNER JOIN pagination pg"
            ."\n    ON " . implode("\n    AND ", $paginationJoinOn) //pg." . $key . " = " . $alias . "." . $key
            ."\nWHERE"
            ."\n        pg.[Rank] >  :offset"
            ."\n    AND pg.[Rank] <= :offsetLimit"
            ."\nORDER BY"
            ."\n    pg.[Rank]"
            ;
        $all['values'] = array_merge($where['values'], array( // where values should come first.
              ':offset'      => $uow->offset
            , ':offsetLimit' => $uow->offset + $uow->limit
        ));
        return $all;
    }

    public static function buildUowGet(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql get query from a non UnitOfWork! Received: " . $uow);
        // throw exception if we don't have an id (?) probably should.
        $get = array(
              'statement' => ''
            , 'values'    => array()
        );
        $record = $uow->table->newInstance($uow->table_id); // reflection class for the record object
        $identity = self::getRecordIdentity($record);
        if (!self::allKeysSet($identity)) // is empty is not sufficient -- we need to account for partial primary keys.
            throw new \CSException("Attempted to get using a record which did not contain a full set of primary keys! Received: " . print_r($identity, true));
        $uow->where = $identity; // overwrite any other part of the where object, if it existed
        $select = self::buildUowSelect($uow);
        $from = self::buildUowFrom($uow);
        $where = self::buildUowWhere($uow);
        $get['statement'] = ""
            ."\n" . $select['statement']
            ."\n" . $from['statement']
            ."\n" . $where['statement']
            ;
        $get['values'] = $where['values']; // shouldn't need any values from select
        return $get;
    }

    public static function buildUowCount(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql count statement from a non UnitOfWork! Received: " . print_r($uow, true));
        $count = array(
            'statement' => ''
        );
        $table = $uow->table->getStaticPropertyValue('table');
        $from = self::buildUowFrom($uow);
        $where = self::buildUowWhere($uow);
        $count['statement'] = ""
            ."\nSELECT COUNT(*) AS [Count]"
            ."\n" . $from['statement']
            ."\n" . $where['statement']
            ;
        $count['values'] = $where['values'];
        return $count;
    }

    public static function buildUowExists(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql exists statement from a non UnitOfWork! Received: " . print_r($uow, true));
        $exists = array(
            'statement' => ''
            , 'values'  => array()
        );
        $alias = $uow->table->getStaticPropertyValue('tableAlias');
        $record = $uow->table->newInstance($uow->table_id); // reflection class for the record object
        // do we want to use exists for more than just the identity? we can change a bit, if necessary.
        $identity = self::getRecordIdentity($record);
        $uow->where = $identity; // overwrite any other part of the where object, if it existed
        $from = self::buildUowFrom($uow);
        $where = self::buildUowWhere($uow);
        $exists['statement'] = ''
            ."\nSELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT TOP 1 " . $alias . ".*"
            ."\n        " . $from['statement']
            ."\n        " . $where['statement']
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS [Exists]";
        $exists['values'] = $where['values'];
        return $exists;
    }

    public static function buildUowUpdate(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql update query from a non UnitOfWork! Received: " . print_r($uow, true));
        if (is_null($uow->table_id) || empty($uow->table_id))
            throw new \InvalidArgumentValueException("Attempted to build a sql update statement on " . $uow->table->getShortName() . " using a UnitOfWork with null or empty table_id(s)!");
        if (!$uow->data instanceof \ClubSpeed\Database\Records\BaseRecord)
            $uow->data = $uow->table->newInstance($uow->data); // either throw exception, or try to make the base record ourselves
        $uow->data = self::stripRecordIdentity($uow->data); // disallow any attempts to update a primary key
        if (Objects::isEmpty($uow->data))
            throw new \InvalidArgumentValueException("Attempted to build sql update statement on " . $uow->table->getShortName() . " with an empty record!");
        $update = array(
            'statement' => ''
            , 'values'  => array()
        );
        $table = $uow->table->getStaticPropertyValue('table');
        $alias = $uow->table->getStaticPropertyValue('tableAlias');
        $columns = array();
        $identity = $uow->table->newInstance($uow->table_id);
        if (!self::allKeysSet($identity)) // is empty is not sufficient -- we need to account for partial primary keys.
            throw new \CSException("Attempted to get using a record which did not contain a full set of primary keys! Received: " . print_r($identity, true));
        $where = self::buildWhere($identity);
        $record = self::stripRecordIdentity($uow->data); // make sure we aren't accidentally updating a primary key.
        foreach($record as $name => $value) {
            if (isset($value)) {
                $param = ":" . $name;
                $columns[] = array(
                    'name' => $name,
                    'alias' => $param
                );
                $update['values'][$param] = ($value === Enums::DB_NULL ? NULL : $value);
            }
        }
        
        foreach($columns as $key => $val)
            $columns[$key] = $alias . "." . $val['name'] . ' = ' . $val['alias'];
        $update['statement'] = ""
            ."\nUPDATE " . $alias
            ."\nSET"
            ."\n      " . implode("\n    , ", $columns)
            ."\nFROM " . $table . " AS " . $alias
            . $where['statement']
            ;
        $update['values'] = array_merge($update['values'], $where['values']);
        return $update;
    }

    public static function buildUowDelete(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql select query from a non UnitOfWork! Received: " . $uow);
        if (is_null($uow->table_id) || empty($uow->table_id))
            throw new \InvalidArgumentValueException("Attempted to build a sql delete statement from a UnitOfWork with null or empty table_id(s)!");
        $delete = array(
              'statement' => ''
            , 'values'    => array()
        );

        $table = $uow->table->getStaticPropertyValue('table');
        $alias = $uow->table->getStaticPropertyValue('tableAlias');
        $record = $uow->table->newInstance($uow->table_id);
        $identity = self::getRecordIdentity($record);
        if (!self::allKeysSet($identity)) // is empty is not sufficient -- we need to account for partial primary keys.
            throw new \CSException("Attempted to delete from " . $table->getShortName() . " using a record which did not contain a full set of primary keys! Received: " . print_r(array_filter($identity), true));
        $uow->where = $identity; // overwrite any other part of the where object, if it existed
        $where = self::buildUowWhere($uow);
        $delete['statement'] = ""
            ."\nDELETE " . $alias
            ."\nFROM " . $table . " AS " . $alias
            ."\n" . $where['statement'];
        $delete['values'] = $where['values'];
        return $delete;
    }

    public static function buildUowSelect(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql select query from a non UnitOfWork! Received: " . $uow);
        $select = array(
              'statement' => ''
            , 'names'     => array()
        );
        $table = $uow->table->getStaticPropertyValue('table');
        $alias = $uow->table->getStaticPropertyValue('tableAlias');
        if (is_null($uow->select)) {
            $uow->select = array();
            $columns = $uow->table->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach($columns as $column) {
                if ($column->isStatic()) // hacky... is it faster just to make a newInstance and foreach that?
                    continue;
                $uow->select[] = $column->name;
            }
        }
        foreach ($uow->select as $column)
            $select['names'][] = $alias . '.' . $column; // do we want the table alias? problematic with the paging.
        $select['names'][0] = '      ' . $select['names'][0]; // for readability (?)
        $select['statement'] = ""
            ."SELECT"
            ."\n" . implode("\n    , ", $select['names'])
            ;
        return $select;
    }

    public static function buildUowFrom(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql select query from a non UnitOfWork! Received: " . $uow);
        $from = array(
            'statement' => ''
        );
        $table = $uow->table->getStaticPropertyValue('table');
        $alias = $uow->table->getStaticPropertyValue('tableAlias');
        $from['statement'] = "FROM " . $table . " AS " . $alias;
        return $from;
    }

    public static function buildUowWhere(&$uow) {
        $table      = $uow->table->getStaticPropertyValue('table');
        $alias      = $uow->table->getStaticPropertyValue('tableAlias');
        $operators  = Comparator::$operators;
        $where      = array(
              'statement' => ''
            , 'values'    => array()
        );
        // what about if uow->table_id? just bypass all logic and return the single?
        // or specific logic for the single, that doesn't even touch this function? <-- probably this.
        $valueCounter = 0; // switching to value counter, since a count is no longer sufficient with support for the IN keyword
        $paramIterator = function() use (&$valueCounter) {
            return ':p' . $valueCounter++;
        };
        $getConnector = function() use (&$where) {
            return (count($where['values']) < 1 ? '    ' : 'AND ');
            // return ($valueCounter === 0 ? '    ' : 'AND '); // also works, but only if $getConnector() comes before $paramIterator()
        };
        if (!is_null($uow->where) && !empty($uow->where)) {
            foreach($uow->where as $key => $data) {
                if (Arrays::isAssociative($data)) {
                    // then we know we have the "mongo" style statements
                    // make sure we do the loop -- we could have multiple applied to one column.
                    foreach($data as $comparatorKey => $comparatorValue) {
                        if (isset($operators[$comparatorKey])) {
                            $operator = $operators[$comparatorKey];
                            $tempColumn = array(
                                  'connector'   => $getConnector()
                                , 'left'        => $alias . '.' . $key
                                , 'operator'    => ' '. $operator . ' '
                            );
                            switch($operator) {
                                case $operators['$is']:
                                case $operators['$isnot']:
                                    // force the comparator value to be NULL.
                                    // we can't parameterize null, so don't bother accepting 
                                    // any sort of user input. should ensure injection safety, as well.
                                    // (there's no need to, with sql server - see https://msdn.microsoft.com/en-us/library/ms188795.aspx)
                                    $tempColumn['right'] = 'NULL';
                                    break;
                                case $operators['$in']:
                                    if (is_string($comparatorValue)) {
                                        $comparatorValue = array_map(
                                            function($x) { return trim(x); }
                                            , explode(",", $comparatorValue)
                                        );
                                    }
                                    $tempParams = array();
                                    foreach($comparatorValue as $rightVal) {
                                        // load the values, store parameter names
                                        $param = $paramIterator();
                                        $where['values'][$param] = $rightVal;
                                        $tempParams[] = $param;
                                    }
                                    // put the list of parameter names in for the statement
                                    $tempColumn['right'] = '(' . implode(', ', $tempParams) . ')';
                                    break;
                                case $operators['$like']:
                                    $param = $paramIterator();
                                    $tempColumn['right'] = $param;
                                    // account for extension key $has -- automatically wrap value in % signs, if it's being used
                                    // note that we could really use a fall-through here,
                                    // the only difference for default and $like is the $has extension.
                                    $where['values'][$param] = ($comparatorKey === '$has' ? '%'.$comparatorValue.'%' : $comparatorValue);
                                    break;
                                default:
                                    // can be handled with standard parameterization
                                    // 1. $gt
                                    // 2. $gte
                                    // 3. $lt
                                    // 4. $lte
                                    // 5. $eq
                                    // 6. $neq
                                    // 7. $lk
                                    // 8. $nlk
                                    $param = $paramIterator();
                                    $tempColumn['right'] = $param;
                                    $where['values'][$param] = $comparatorValue;
                            }
                            if (!empty($tempColumn))
                                $where['columns'][] = $tempColumn;
                        }
                    }
                }
                else if (is_array($data)) {
                    // assume we have an "IN" / contains style statement,
                    // since we had an array from json that was NOT an object
                    $tempColumn = array(
                          'connector'   => $getConnector()
                        , 'left'        => $alias . '.' . $key
                        , 'operator'    => ' IN '
                    );
                    $tempParams = array();
                    foreach($data as $rightVal) {
                        $param = $paramIterator();
                        // load the values, store parameter names
                        // $param = ':p' . $valueCounter++;
                        $where['values'][$param] = $rightVal;
                        $tempParams[] = $param;
                    }
                    // put the list of parameter names in for the statement
                    $tempColumn['right'] = '(' . implode(', ', $tempParams) . ')';
                    $where['columns'][] = $tempColumn;
                }
                else {
                    if (!is_null($data)) {
                        // we just want to check for direct equality
                        $param = $paramIterator();
                        $where['columns'][] = array(
                              'connector' => $getConnector()
                            , 'left'      => $alias . "." . $key
                            , 'operator'  => ' ' . $operators['$eq'] . ' '
                            , 'right'     => $param
                        );
                        $where['values'][$param] = $data;
                    }
                }
            }
        }
        // build the sql clause, if necessary
        if (!empty($where['columns'])) {
            foreach($where['columns'] as $key => $val) {
                $where['columns'][$key] = "\n        "
                    . @$val['connector'] . ''
                    . $val['left']
                    . $val['operator']
                    . $val['right']
                    ;
            }
            $where['statement'] = "WHERE" . implode("", $where['columns']);
        }
        return $where;
    }


    // -------------------
    // BEGIN OLD METHODS
    // -------------------

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