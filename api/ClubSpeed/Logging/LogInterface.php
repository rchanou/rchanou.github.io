<?php

namespace ClubSpeed\Logging;

interface LogInterface {
    public function debug($message, $namespace = null);
    public function error($message, $namespace = null, \Exception $exception = null);
    public function info($message, $namespace = null);
    public function log($message, $namespace = null);
    public function warn($message, $namespace = null);
}