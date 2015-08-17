<?php

namespace ClubSpeed\Logic;

/**
 * The base class for ClubSpeed API logic classes
 * which are placed on top of a read only resource.
 */
abstract class BaseReadOnlyLogic extends BaseLogic {

    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->before('uow', function($uow) {
            switch($uow->action) {
                case 'create':
                case 'update':
                case 'delete':
                    throw new \CSException("Attempted a " . $uow->action . " on a read-only resource!");
            }
        });
    }

    public function create($params = array(), $callback = null) {
        throw new \CSException("Attempted a create on a read-only resource!");
    }

    public function update() {
        throw new \CSException("Attempted an update on a read-only resource!");
    }

    public function delete() {
        throw new \CSException("Attempted a delete on a read-only resource!");
    }
}