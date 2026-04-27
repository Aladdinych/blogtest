<?php

error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

if (!defined("BASE_PATH")) {
    define("BASE_PATH", realpath(dirname(realpath(__FILE__)) . '/../'));
}

define('DB_HOST', 'MySQL-5.6'); 
define('DB_NAME', 'myblog');
define('DB_USER', 'root'); 
define('DB_PASS', ''); 


