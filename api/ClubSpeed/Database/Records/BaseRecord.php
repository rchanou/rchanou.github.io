<?php

namespace ClubSpeed\Database\Records;
use ClubSpeed\Utility\Arrays;
use ClubSpeed\Utility\Convert;
use ClubSpeed\Utility\Types;

abstract class BaseRecord {

    // protected static $_definition = null; // doesn't actually need to be declared when we use static::

    public function __construct($data = array()) {
        call_user_func_array(array($this, 'load'), func_get_args());
    }

    // // remove in the future. not really helpful.
    // public function validate($type) {
    //     return true; // override as necessary
    // }

    public function convert() {
        $definition = self::definition();
        $columns = $definition['columns'];
        foreach($this as $name => $val) {
            if (isset($this->{$name}) && !is_null($this->{$name}))
                $this->{$name} = Convert::convert($val, Types::byName($columns[$name]['type']));
        }
    }

    // test performance of this. we can also define in memory. 
    // if we can get rid of the need for a $this to infer the name, we can use a static.
    public static function definition() {
        if (empty(static::$_definition)) {
            $reflection = new \ReflectionClass(get_called_class()); // static hackiness, to avoid a need for $this
            $name = $reflection->getShortName();
            $filename = dirname(__FILE__) . '\\..\\Definitions\\' . $name . '.json';
            if (!file_exists($filename))
                throw new \CSException('Attempted to use ' . get_called_class() . ' without providing a record definition at ' . $filename . '!');
            $string = file_get_contents($filename);
            $json = json_decode($string, true);
            if (empty($json))
                throw new \CSException('Attempted to use ' . get_called_class() . ' with an un-parseable record definition at ' . $filename . '!');
            foreach($json['columns'] as $key => $column) {
                $json['columns'][$column['name']] = $column; // make columns accessible by name
                unset($json['columns'][$key]); // drop the integer indices
            }
            static::$_definition = $json;
        }
        return static::$_definition;
    }

    public function load() {
        $args = func_get_args(); // hackyness required for loading records with composite primary keys.
        if (count($args) > 0) {
            $data = end($args);
            if (!empty($data)) {
                $definition = static::definition();
                if (is_array($data) && Arrays::isAssociative($data)) {
                    $columns = $definition['columns'];
                    foreach($data as $name => $val) {
                        if (isset($columns[$name]) && !is_null($val)) {
                            /*
                                performance (100 customers)
                                external convert with date:     70-90ms
                                external convert without date:  20-25ms
                                internal convert with date:     55-70ms (winner, loses ability to update and set nulls)
                                internal convert without date:  10-12ms
                            */
                            // $type = $columns[$name]['type'];
                            // switch($type) {
                            //     case "boolean":
                            //     case "bit":
                            //         // $this->{$name} = Convert::convert($val, Types::$boolean);
                            //         $this->{$name} = (boolean)$val;
                            //         break;
                            //     case "date":
                            //     case "datetime":
                            //         $this->{$name} = Convert::convert($val, Types::$date);
                            //         // $this->{$name} = $val;
                            //         break;
                            //     case "bigint":
                            //     case "identity":
                            //     case "int":
                            //     case "integer":
                            //         // $this->{$name} = Convert::convert($val, Types::$integer);
                            //         $this->{$name} = (int)$val;
                            //         break;
                            //         // return Types::$integer;
                            //     case "double":
                            //     case "decimal":
                            //     case "float":
                            //     case "money":
                            //     case "numeric":
                            //         // $this->{$name} = Convert::convert($val, Types::$double);
                            //         $this->{$name} = (double)$val;
                            //         break;
                            //     case "ntext":
                            //     case "varchar":
                            //     case "nvarchar":
                            //     case "string":
                            //         // $this->{$name} = Convert::convert($val, Types::$string);
                            //         $this->{$name} = (string)$val;
                            //         break;
                            //     case "null":
                            //         $this->{$name} = null; // todo, if necessary
                            //         break;
                            //     default:
                            //         pr($type);
                            //         $this->{$name} = $val;
                            // }

                            /*
                                performance (100 customers): 5ms (no conversions, just column checking)
                            */
                            // $this->{$name} = $val;

                            /*
                                performance (100 customers)
                                with dates: 70-90ms (winner for usability and extendability. performance still bad, from dates.)
                                without dates: 20-30ms
                            */
                            $this->{$name} = Convert::convert($val, Types::byName($columns[$name]['type']));
                            
                            /*
                                performance (100 customers)
                                with dates: 90-115ms
                                without dates: 30-40ms

                                note, this will almost always call Types::byName internally, 
                                and if it does have to call Types::byName internally,
                                it will end up making a recursive call as well.

                                may as well call it ahead of time, to save the recursion.
                            */
                            // $this->{$name} = Convert::convert($val, $columns[$name]['type']);
                        }
                    }
                }
                else {
                    // assume $args contains all primary keys
                    $keyNames = $definition['keys'];
                    $keyValues = array();
                    if (count($keyNames) === count($args))
                        $keyValues = $args;
                    else if (count($keyNames) === count($data))
                        $keyValues = $data; // keys passed in as Instance(array(key1, key2))
                    else // wrong number of keys passed, throw exception here, or return empty record?
                        return;
                    $c = count($keyNames);
                    for($i = 0; $i < $c; $i++) {
                        $keyName = $keyNames[$i];
                        $keyValue = $keyValues[$i];
                        $keyType = Arrays::where($definition['columns'], function($x) use ($keyName) {
                            return $x && $x['name'] === $keyName;
                        });
                        $keyType = Arrays::select($keyType, function($x) {
                            return $x['type'];
                        });
                        $keyType = Arrays::first($keyType);
                        $this->{$keyNames[$i]} = $keyValues[$i]; // Convert::convert($keys[$i], self::$definition[self::$key[$i]]);
                    }
                }
            }
            else {
                // do anything?
            }
        }

        // $this->convert(); // not any faster, but re-usable if we want to ensure type safety after a load?
        // $sw->pop();
    }
}