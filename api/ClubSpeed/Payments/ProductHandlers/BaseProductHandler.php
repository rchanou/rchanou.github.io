<?php

namespace ClubSpeed\Payments\ProductHandlers;

abstract class BaseProductHandler {

    protected $logic;
    protected $db;
    protected $webapi;

    public function __construct(&$logic, &$db) {
        $this->logic = $logic;
        $this->db = $db;
        $this->webapi = $GLOBALS['webapi'];
    }

    abstract public function handle($checkTotal, $metadata = array());
}