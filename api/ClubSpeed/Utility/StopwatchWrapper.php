<?php

namespace ClubSpeed\Utility;
require_once(__DIR__.'./pr.php');
require_once(__DIR__.'./Stopwatch.php');

class StopwatchWrapper {
    private $_sw;
    private $_taskDescription;

    public function __construct($taskDescription) {
        $this->_taskDescription = $taskDescription;
        $this->_sw = new \ClubSpeed\Utility\Stopwatch();
        $this->_sw->start();
    }

    public function stop() {
        $this->_sw->stop();
        // allow pr to determine whether or not to actually print the details
        pr($this->_taskDescription . " took " . $this->_sw->duration() . " ms");
    }
}