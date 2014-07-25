<?php
$apiUrl = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' . $_SERVER['HTTP_HOST'] . '/api/index.php' : 'https://' . $_SERVER['HTTP_HOST'] . '/api/index.php';
$apiKey = '38ajdfkshx';
$debugging = true;