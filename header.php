<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) 
    ? "https://" 
    : "http://";

define("BASE_URL", $protocol . $_SERVER['HTTP_HOST'] . '/daily-time-record');


if (!defined('TEMP')) {
    define('TEMP', __DIR__ . '/includes/templates/');
}

if (!defined('SRC')) {
    define('SRC', __DIR__ . '/includes/src/');
}

require_once  __DIR__ . '/includes/functions.php';

sessionStart();