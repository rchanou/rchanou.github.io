<?php

namespace ClubSpeed\Payments\ProductHandlers;

abstract class BaseProductHandler {

    protected $logic;

    public function __construct(&$logic) {
        $this->logic = $logic;
    }

    abstract public function handle($checkTotal, $metadata = array());
}