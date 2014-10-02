<?php

namespace ClubSpeed\Utility;

class Stopwatch {
    private $_startTime;
    private $_stopTime;
    private $_running;

    private function currentTime() {
        // get current time in ms
        return microtime(true) * 1000;
    }

    public function __construct() {
        $this->reset();
    }

    public function start() {
        if (!$this->_running) {
            $this->_stopTime = null;
            $this->_running = true;
            $this->_startTime = $this->currentTime();
        }
    }

    public function stop() {
        if ($this->_running) {
            $this->_stopTime = $this->currentTime();
            $this->_running = false;            
        }
    }

    public function duration() {
        if (!$this->_running) {
            return ($this->_stopTime - $this->_startTime);
        }
        else {
            return ($this->currentTime() - $this->_startTime);
        }
    }

    public function reset() {
        $this->_startTime = null;
        $this->_stopTime = null;
        $this->_running = false;
    }
}