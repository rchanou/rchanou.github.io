<?php

function pr($data) {
    // note: this should not be active in any live api for security purposes
    if (filter_var(@$_REQUEST['debug'], FILTER_VALIDATE_BOOLEAN)) {
        echo '<pre>';
        print_r($data);
        echo'</pre>';
    }
}