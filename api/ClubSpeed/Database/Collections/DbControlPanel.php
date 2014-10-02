<?php

namespace ClubSpeed\Database\Collections;

class DbControlPanel extends DbCollection {

    public function __construct($db) {
        $this->definition = new \ReflectionClass('\ClubSpeed\Database\Records\ControlPanel');
        parent::__construct($db);
    }

    // we really only want to expose match and find methods, throw exceptions for all others

    public function create($data) {
        throw new \BadMethodCallException("Attempted to create a ControlPanel record!");
    }

    public function batchCreate($data) {
        throw new \BadMethodCallException("Attempted to batchCreate ControlPanel records!");
    }

    public function get($id) {
        throw new \BadMethodCallException("Attempted to get a ControlPanel record by id!");
    }

    public function update($data) {
        throw new \BadMethodCallException("Attempted to update a ControlPanel record!");
    }

    public function delete($data) {
        throw new \BadMethodCallException("Attempted to delete a ControlPanel record!");
    }

    public function exists($data) {
        throw new \BadMethodCallException("Attempted to check existence of a ControlPanel record!");
    }
}