<?php
namespace Classes;

trait SmartyInit {

	public $smarty;

	public function smartyInit() {

		$this->smarty = new \Smarty\Smarty;

		$this->smarty->debugging = false;
		$this->smarty->caching = false;
		$this->smarty->cache_lifetime = 120;
		$this->smarty->setTemplateDir = BASE_PATH.'templates/';
		$this->smarty->setCompileDir = BASE_PATH.'smarty/templates_c/';
		$this->smarty->setConfigDir = BASE_PATH.'smarty/config/';
		$this->smarty->setCacheDir = BASE_PATH.'smarty/cache/';

	}
}

?>