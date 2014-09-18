<?php
// Get private key -- should secure with private key in _GET?
require_once('../config.php');

$files = scandir('.');

$ignored_files = array('.', '..', 'index.php');

echo '<h1>Migrations</h1>';

echo '<ol>';
foreach($files as $file) {
  if(!in_array($file, $ignored_files) && substr($file, -3) === 'php') {
    echo '<li><a href="' . $file . '" target="_blank">' . $file . '</a></li>';
  }
}
echo '</ol>';