<?php
if(!defined("BASE_PATH"))
	define("BASE_PATH", realpath(dirname(realpath(__FILE__)) ) . DIRECTORY_SEPARATOR);

require_once(BASE_PATH.'smarty/Smarty.class.php');
require_once(BASE_PATH.'classes/page.php');
global $page,$smarty;

$smarty = new \Smarty\Smarty;

$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 120;
$smarty->setTemplateDir = BASE_PATH.'templates/';
$smarty->setCompileDir = BASE_PATH.'smarty/templates_c/';
$smarty->setConfigDir = BASE_PATH.'smarty/config/';
$smarty->setCacheDir = BASE_PATH.'smarty/cache/';

$page = new Page();

$page->goPage();


?>