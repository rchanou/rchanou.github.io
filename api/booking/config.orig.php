<?php
$apiUrl = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' . $_SERVER['HTTP_HOST'] . '/api/index.php' : 'https://' . $_SERVER['HTTP_HOST'] . '/api/index.php';
$apiKey = '';
$debugging = true;