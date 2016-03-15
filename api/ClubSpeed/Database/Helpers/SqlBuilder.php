<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Database\Helpers\Comparator;
use ClubSpeed\Database\Records\BaseRecord;
use ClubSpeed\Database\Statements\StatementFactory;
use ClubSpeed\Enums\Enums;
use ClubSpeed\Structures\TreeNode;
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
        $def = $record->definition();
        $keys = Arrays::select($def['keys'], function($x) { return $x['name']; });
        if (!is_array($keys))
            $keys = array($keys); // shouldn't be hit
        foreach($tempRecord as $prop => $val) {
            if ($callback($prop, $keys))
                $tempRecord->$prop = null;
        }
        return $tempRecord;
    }

    private static function allKeysSet($record) {
        $def = $record->definition();
        $keys = $def['keys'];
        if (!is_array($keys))
            $keys = array($keys);
        foreach($keys as $key) {
            if (is_null($record->{$key['name']}))
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
        if (!$uow->data instanceof BaseRecord) // either throw exception, or try to make the base record ourselves
            $uow->data = $uow->table->newInstance($uow->data);
            // throw new \InvalidArgumentException("Attempted to build a sql create query with a non BaseRecord! Received: " . print_r($uow->data, true));
        if (Objects::isEmpty($uow->data))
            throw new \InvalidArgumentValueException("Attempted to build sql insert statement with an empty record! Received: " . print_r($uow->data, true));
        $create = array(
              'names'   => array()
            , 'values'  => array()
            , 'aliases' => array()
        );
        $table = $uow->definition['table']['name'];
        $keys = $uow->definition['keys'];
        $hasIdentityKey = false;
        $identityKeys = Arrays::where($keys, function($x) { return isset($x['identity']) && $x['identity'] === true; });
        foreach($uow->data as $name => $value) {
            if (isset($value) && $value !== Enums::DB_NULL) {
                $create['names'][]   = $name;
                $create['values'][]  = $value;
                $create['aliases'][] = ":" . $name;
                if (Arrays::contains($identityKeys, function($x) use ($name) { return $x['name'] === $name; }))
                    $hasIdentityKey = true; // we are attempting to insert a primary key. allow it, but set IDENTITY_INSERT on
            }
        }
        $create['statement'] = ""
            . ($hasIdentityKey ? "\nSET IDENTITY_INSERT " . $table . " ON;" : '')
            ."\nINSERT INTO " . $table . " ("
            ."\n      " . implode("\n    , ", $create['names'])
            ."\n)"
            ."\nVALUES ("
            ."\n      " . implode("\n    , ", $create['aliases'])
            ."\n)"
            . ($hasIdentityKey ? "\nSET IDENTITY_INSERT " . $table . " OFF;" : '')
            ;

        return $create;
    }

    public static function buildUowAll(&$uow) {
        if (!$uow instanceof \ClubSpeed\Database\Helpers\UnitOfWork)
            throw new \InvalidArgumentException("Attempted to build sql all query from a non UnitOfWork! Received: " . $uow);
        $all = array(
              'statement' => ''
            , 'values'    => array()
        );

        $keys  = $uow->definition['keys']; // check to see if we have legitimate primary keys before building pagination?
        $table = $uow->definition['table']['name'];
        $alias = $uow->definition['table']['alias'];

        // the order portion of UnitOfWork is specific to 'all' actions.
        $order = array();
        if (!empty($uow->order)) { // check against empty, in case the user provided all invalid column names for order, which would result in an empty array
            foreach($uow->order as $column => $direction)
                $order[] = $alias . "." . $column . " " . $direction;
        }
        else {
            // no order is provided. just use the key ascending for the ROW_NUMBER().
            foreach($keys as $column)
                $order[] = $alias . "." . $column['name'] . " ASC";
        }
        $order = implode(', ', $order);

        $select = self::buildUowSelect($uow);
        $from = self::buildUowFrom($uow);
        $where = self::buildUowWhere($uow);

        $all['statement'] = ""
            ."\n". $select['statement']
            ."\nFROM ("
            ."\n    SELECT"
            ."\n        *"
            ."\n        , ROW_NUMBER() OVER (ORDER BY " . $order . ") AS [Rank]"
            ."\n    " . $from['statement']
            . (!empty($where['statement']) ? "\n    " . $where['statement'] : '')
            ."\n) " . $alias
            ."\nWHERE"
            ."\n        " . $alias . ".[Rank] >  :offset"
            ."\n    AND " . $alias . ".[Rank] <= :offsetLimit"
            ."\nORDER BY"
            ."\n    ". $alias . ".[Rank]"
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
        $alias = $uow->definition['table']['alias'];
        if (!empty($uow->table_id)) {
            $record = $uow->table->newInstance($uow->table_id);
            $identity = self::getRecordIdentity($record);
            $uow->where = $identity; // overwrite any existing portion of the $where? or append?
        }
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
        if (!$uow->data instanceof BaseRecord)
            $uow->data = $uow->table->newInstance($uow->data); // either throw exception, or try to make the base record ourselves
        $uow->data = self::stripRecordIdentity($uow->data); // disallow any attempts to update a primary key
        if (Objects::isEmpty($uow->data))
            throw new \InvalidArgumentValueException("Attempted to build sql update statement on " . $uow->table->getShortName() . " with an empty record!");
        $update = array(
            'statement' => ''
            , 'values'  => array()
        );
        $table = $uow->definition['table']['name'];
        $alias = $uow->definition['table']['alias'];
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
        $table = $uow->definition['table']['name'];
        $alias = $uow->definition['table']['alias'];
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
        $table = $uow->definition['table']['name'];
        $alias = $uow->definition['table']['alias'];
        if (is_null($uow->select) || empty($uow->select)) {
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
        $table = $uow->definition['table']['name'];
        $alias = $uow->definition['table']['alias'];
        $from['statement'] = "FROM " . $table . " AS " . $alias;
        return $from;
    }

    public static function buildUowWhere(&$uow) {
        if ($uow->where instanceof BaseRecord) {
            $query = (array)$uow->where;
            // strip any nulls from the record, since they will default to null
            // convert any DB_NULLS to be nulls for the tree parser to use
            foreach($query as $key => $val) {
                if (is_null($val))
                    unset($query[$key]);
                else if ($val === Enums::DB_NULL)
                    $query[$key] = null;
            }
            $uow->where = $query;
        }

        if (empty($uow->where)) {
            return array(
                'statement' => '',
                'values' => array()
            );
        }
        else {
            $tree = self::makeQueryTree($uow->where);
            $where = self::parseTree($tree, $uow->definition['table']['alias']);
            $where['statement'] = empty($where['statement']) ? '' : ("WHERE " . $where['statement']);
            return $where;
        }
    }

    private static function makeQueryTree($where) {
        $operators = Comparator::$operators; // consider making parameter
        $children = array();
        foreach ($where as $key => $val) {
            if ($key === '$and' || $key === '$or') {
                // special case, should always have its own children
                // $val *must* be an array
                $_node = new TreeNode(array('connector' => $operators[$key]));
                foreach($val as $obj)
                    $_node->add(self::makeQueryTree($obj));
                if (!$_node->isLeaf()) // only add if the node had parsable children
                    $children[] = $_node;
            }
            else if ($key === '$not') {
                // $val *must* be an object.
                $_node = new TreeNode(array('connector' => $operators['$not']));
                $_node->add(self::makeQueryTree($val)); // only one child -- could be $and or $or, though.
                $children[] = $_node;
            }
            else {
                if (Arrays::isAssociative($val)) {
                    $left = $key;
                    foreach($val as $opKey => $right) {
                        $operator = $operators[$opKey];
                        // special conversion: if we have $eq or $neq null,
                        // we need to use $is and $isnot instead, respectively
                        if ($right === null || $right === Enums::DB_NULL) {
                            if ($operator === $operators['$eq'])
                                $operator = $operators['$is'];
                            else if ($operator === $operators['$ne'])
                                $operator = $operators['$isnot'];
                        }
                        $statement = StatementFactory::make(array(
                              'left'     => $left
                            , 'operator' => $operator
                            , 'right'    => $right
                        ));
                        $children[] = new TreeNode($statement);
                    }
                }
                else if (is_array($val)) {
                    $statement = StatementFactory::make(array(
                          'left'     => $key
                        , 'operator' => $operators['$in']
                        , 'right'    => $val
                    ));
                    $children[] = new TreeNode($statement);
                }
                else {
                    $operator = ($val === null || $val === Enums::DB_NULL)
                        ? $operators['$is']
                        : $operators['$eq'];
                    $statement = StatementFactory::make(array(
                          'left'     => $key
                        , 'operator' => $operator
                        , 'right'    => $val
                    ));
                    $children[] = new TreeNode($statement);
                }
            }
        }

        // count the number of children we have.
        // if we have more than one, then we need to connect them
        // using an additional '$and' node for the implied connector
        if (count($children) === 1)
            $node = $children[0];
        else {
            $node = new TreeNode(array('connector' => $operators['$and']));
            foreach ($children as $child)
                $node->add($child);
        }

        return $node;
    }

    private static function indent($depth = 0) {
        return str_repeat(" ", $depth * 4);
    }

    private static function parseTree($node, $alias, $depth = 1, $counter = 0) {
        // do we need the "empty" base case to be taken care of, or do we catch it early?
        $statements = array();
        $values = array();
        $operators = Comparator::$operators; // consider making parameter
        if (!$node->isLeaf()) {
            $connector = $node->value['connector']; // what about when connector is $not ?
            if ($connector === $operators['$not']) {
                $child = $node->children[0];
                $parsed = self::parseTree($child, $alias, $depth + 1, $counter);
                $statement = "NOT (\n"
                    . self::indent($depth + 1)
                    . $parsed['statement']
                    . "\n"
                    . self::indent($depth)
                    . ")";
                $values = array_merge($values, $parsed['values']);
            }
            else /* $and, $or */ {
                foreach($node->children as $child) {
                    $parsed = self::parseTree($child, $alias, $depth + 1, $counter);
                    $statements[] = $parsed['statement'];
                    $values = array_merge($values, $parsed['values']);
                    $counter += count($parsed['values']);
                }
                $statement = "(\n"
                    . self::indent($depth + 1)
                    . Arrays::join(
                        $statements,
                        (
                            "\n"
                            . self::indent($depth + 1)
                            . $connector
                            . ' '
                        )
                    )
                    . "\n"
                    . self::indent($depth)
                    . ")";
            }
            return array(
                'statement' => $statement,
                'values' => $values
            );
        }
        else
            return $node->value->build();
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
        $definition = $record->definition();
        $table = $definition['table']['name'];
        $insert['statement'] = ""
            ."\nINSERT INTO " . $table . " ("
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
        $definition = $record->definition();
        $table = $definition['table']['name'];
        $alias = $definition['table']['alias'];
        $select = array(
            'names'         => array()
            , 'table'       => $table
            , 'tableAlias'  => $alias
        );
        foreach ($record as $name => $value) {
            if (isset($name)) { // probably not necessary
                $select['names'][] = $alias . '.' . $name;
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
        if ($definition instanceof \ReflectionClass)
            $record = $definition->newInstance();
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
        $definition = $record->definition();
        $alias = $definition['table']['alias'];
        $table = $definition['table']['name'];
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
                    , 'right'       => $alias . '.' . $comparator->right
                    , 'connector'   => isset($connector) ? $connector . ' ' : null
                );
                $where['values'][$param] = $comparator->left; // alias it to protect against injection
            }
            else if (is_array($comparator->right) || !property_exists($record, $comparator->right)) {
                // right is a value or an array for IN
                // check for IS(?: NOT) NULL special case
                if ($comparator->right != 'NULL') { // convert to DB_NULL at some point -- make map do the conversion (?)
                    $tempColumn = array(
                          'left'        => $alias . '.' . $comparator->left
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
                          'left'        => $alias . '.' . $comparator->left
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
            throw new \CSException("Attempted to build a where clause for " . $table . " without any columns!");

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
        $definition = $record->definition();
        $table = $definition['table']['name'];
        $alias = $definition['table']['alias'];

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
        $tablePrefix = $alias . '.';
        foreach($update['columns'] as $key => $val) {
            $update['columns'][$key] = "\n    "
                . $tablePrefix
                . $val['name']
                . ' = '
                . $val['alias']
                ;
        }
        $update['statement'] = ""
            ."\nUPDATE " . $alias
            ."\nSET"
            . implode(", ", $update['columns'])
            ."\nFROM " . $table . ' AS ' . $alias
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
        $definition = $record->definition();
        $table = $definition['table']['name'];
        $alias = $definition['table']['alias'];
        $where = self::buildWhere($record);
        $delete['statement'] = ""
            ."\nDELETE " . $alias
            ."\nFROM " . $table . ' AS ' . $alias
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
        $definition = $record->definition();
        $table = $definition['table']['name'];
        $alias = $definition['table']['alias'];
        $where = self::buildWhere($identity);
        $exists = array();
        $exists['statement'] = ''
            ."\nSELECT"
            ."\n    CASE WHEN EXISTS ("
            ."\n        SELECT TOP 1 " . $alias . ".*"
            ."\n        FROM " . $table . " " . $alias
            ."\n        " . $where['statement'] // $record::$tableAlias . "." . $record::$key . " = :" . $record::$key
            ."\n    )"
            ."\n    THEN 1"
            ."\n    ELSE 0"
            ."\n    END AS [Exists]";
        $exists['values'] = $where['values'];
        return $exists;
    }
}
