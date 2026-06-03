<?php
namespace Classes;

error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

if(!defined("BASE_PATH")){
	define("BASE_PATH", realpath(dirname(realpath(__FILE__)) ) . DIRECTORY_SEPARATOR);
}


require_once(BASE_PATH.'vendor/autoload.php');

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Classes\Page;

$page = new Page;
$page->goPage();


?>