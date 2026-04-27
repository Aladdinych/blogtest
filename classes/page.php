<?php
class Page{

public $title;
public $template;
public $layout;
public $metadescription;
public $metakeywords;
public $content;
public $uriparams;
public $url;
public $module;
public $npage;
public $perpage;

function __construct($layout=null){

	$this->layout = (!isset($layout)) ? 'templates/layout.tpl' : $layout;
	if(session_id() == '') 
		session_start(); 

	$this->uriparams = $this->getUriParams();
	$this->module = $this->getModule();
	$this->npage = (isset($this->uriparams['page'])) ? $this->uriparams['page'] : 1;
	$this->perpage = 20;
}

private function getModule($module = null){
	if(!isset($module)) {
		if(empty($this->uriparams['whatdo'])) {
			$module = 'main.php';
		}else{
			$module = $this->uriparams['whatdo'].'.php';
		}
	}
	return $module;
}
private function getUriParams(){
	$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$this->url = $url_path;
	$uri_parts = explode('/', trim($url_path, ' /'));
	$params = [];
	$params['whatdo'] = $uri_parts[0];
	for($i=1; $i < count($uri_parts); $i+=2){
		$params[$uri_parts[$i]] = $uri_parts[$i+1];
	}
	return $params;
}
public function goPage($module = null){
	$module = $this->module;
	if(file_exists(BASE_PATH.$module)) {
		require(BASE_PATH.$module);
	}else{
		echo 'Модуль '.$module.' не найден!';
	}
	exit();
}

public function cutParamFromUrl($name){
	$url = preg_replace(['/page\/\d*\//','/main\//'],'',$this->url);
	return $url;
}

public function dateFormatWithMonthStr($date){
$months = [
	'Jan' => 'Янв',
	'Feb' => 'Фев',
	'Mar' => 'Мар',
	'Apr' => 'Апр',
	'May' => 'Май',
	'Jun' => 'Июн',
	'Jul' => 'Июл',
	'Aug' => 'Авг',
	'Sep' => 'Сен',
	'Oct' => 'Окт',
	'Nov' => 'Ноя',
	'Dec' => 'Дек'
];
$dt = DATE('M d, Y',strtotime($date));
$dt_m = DATE('M',strtotime($date));
$dt = str_replace($dt_m,$months[$dt_m],$dt);
return $dt;
}

}

?>