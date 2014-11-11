<?php

return array(
    'apiURL' => 'http://' . $_SERVER['HTTP_HOST'] . '/api/index.php',
    'apiKey' => 'cs-dev',
    'privateKey' => 'INSERT_PRIVATE_KEY_HERE',
    'dateFormat' => 'Y-m-d',
    'debugging' => false,
    'assetsURL' => 'http://' . $_SERVER['HTTP_HOST'] . '/assets/booking',
    'maxRacers' => 50, //Controls maximum range of dropdown on the first page
    'locale' => 'en_US', //Used for number formatting - http://www.oracle.com/technetwork/java/javase/javase7locales-334809.html
    'currency' => 'USD' //Used for money formatting - http://www.xe.com/iso4217.php
);