<?php

namespace ClubSpeed\Logging;

interface LogInterface {
    public function log($message);
    public function debug($message);
    public function error($message);
}