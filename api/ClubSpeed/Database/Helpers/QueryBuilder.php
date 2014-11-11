<?php

namespace ClubSpeed\Database\Helpers;
use ClubSpeed\Database\Records\BaseRecord;

class QueryBuilder {

    protected $definition;
    protected $_select;

    public $statement;
    public $params;

    public function __construct($definition) {
        if (!$definition instanceof \ReflectionClass || !$definition->isInstance(BaseRecord)) {
            throw new \InvalidArgumentException("QueryBuilder received a definition which was not the reflection of a BaseRecord!");
        }
        $this->definition = $definition;

        // use context, or use record?
    }

    public function attach($record) {
        if (!$record instanceof BaseRecord)
            throw new \InvalidArgumentException("Attempted to attach a non BaseRecord to a QueryBuilder!");
        $this->record = $record;
        return $this;
    }

    public function from($table) {
        if (strpos($table, " " ) === false) {
            // no alias

        }
    }

    public function select() {
        $select = SqlBuilder::buildSelect($this->record);
        $this->statement = $select['statement'];
        $this->params = $select['params'];
        return $this;
    }

    public function update() {
        $update = SqlBuilder::buildUpdate($this->record);
        return $this;
    }

    public function delete() {
        return $this;
    }

    public function from() {
        return $this;
    }

    public function send() {
        // build query
        // send sql
        // get result
    }

}