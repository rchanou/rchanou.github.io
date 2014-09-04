<?php

require_once(__DIR__.'./pr.php');

function loopTimer($loops = 1, $closure) {
    $start = microtime(true);
    for ($i = 0; $i < $loops; $i++) {
        $closure();
    }
    $total = microtime(true) - $start;
    $avg = $total / $loops;
    // pr('Execute Test took an average of: ' . $avg . 'ms');
}