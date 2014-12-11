<?php

namespace ClubSpeed\Payments\ProductHandlers;

abstract class BaseProductHandler {

    protected $logic;
    protected $webapi;

    public function __construct(&$logic) {
        $this->logic = $logic;
        $this->webapi = $GLOBALS['webapi'];
    }

    abstract public function handle($checkTotal, $metadata = array());
}