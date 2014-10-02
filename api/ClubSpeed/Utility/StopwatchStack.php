<?php

namespace ClubSpeed\Utility;
require_once(__DIR__.'/StopwatchWrapper.php');

class StopwatchStack {
    private $_stopwatchStack;

    public function __construct() {
        $this->_stopwatchStack = array();
    }

    public function push($taskDescription) {
        $this->_stopwatchStack[] = new \ClubSpeed\Utility\StopwatchWrapper($taskDescription);
    }

    public function pop() {
        if(count($this->_stopwatchStack) > 0) {
            $_sw = array_pop($this->_stopwatchStack);
            $_sw->stop();
        }
    }
}