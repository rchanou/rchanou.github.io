<?php

namespace ClubSpeed\Utility;

require_once(__DIR__.'./pr.php');

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

class Stopwatch {
    private $_startTime;
    private $_stopTime;
    private $_running;

    public function __construct() {
        $this->reset();
    }

    public function start() {
        if (!$this->_running) {
            $this->_stopTime = null;
            $this->_running = true;
            $this->_startTime = microtime(true);
        }
    }

    public function stop() {
        if ($this->_running) {
            $this->_stopTime = microtime(true);
            $this->_running = false;            
        }
    }

    public function duration() {
        if (!$this->_running) {
            return ($this->_stopTime - $this->_startTime);
        }
        else {
            return (microtime(true) - $this->_startTime);
        }
    }

    public function reset() {
        $this->_startTime = null;
        $this->_stopTime = null;
        $this->_running = false;
    }
}

$sw = new StopwatchStack();