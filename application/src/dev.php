<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function debugEx($str) {
    var_dump($str);
    exit;
}

function debug($str) {
    echo '<pre>';
    var_dump($str);
    echo '</pre>';
}
