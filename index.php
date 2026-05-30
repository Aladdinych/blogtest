<?php
namespace Classes;

if(!defined("BASE_PATH"))
	define("BASE_PATH", realpath(dirname(realpath(__FILE__)) ) . DIRECTORY_SEPARATOR);

require_once(BASE_PATH.'vendor/autoload.php');

use Classes\Page;


$page = new Page;

$page->goPage();


?>