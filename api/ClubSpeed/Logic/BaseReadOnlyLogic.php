<?php

namespace ClubSpeed\Logic;

/**
 * The base class for ClubSpeed API logic classes
 * which are placed on top of a read only resource.
 */
abstract class BaseReadOnlyLogic extends BaseLogic {

    public function create($params = array(), $callback = null) {
        throw new \CSException("Attempted a create on a read-only resource!");
    }

    public function update($onlineBookingsId, $params = array()) {
        throw new \CSException("Attempted an update on a read-only resource!");
    }

    public function delete($id) {
        throw new \CSException("Attempted a delete on a read-only resource!");
    }
}