<?php
namespace Classes;

use Classes\Route;
use Classes\myBlogController;

class Page{

public $title;
public $metadescription;
public $metakeywords;
public $content;
public $uriparams;
public $url;
public $npage;
public $perpage;
private $action;
private $routedata;

function __construct(){

	if(session_id() == '') 
		session_start(); 

	$this->uriparams = $this->getUriParams();
	$this->npage = (isset($this->uriparams['page'])) ? $this->uriparams['page'] : 1;
	$this->perpage = 20;
}

private function getUriParams(){
	$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$this->url = $url_path;
	$routedata = Route::getRouteData($url_path);
	$params = [];
	if(!empty($routedata)){
		$this->routedata = $routedata;
		$this->action = $routedata['action'];
		if(!empty($routedata['params']) && $routedata['params'] !== '/'){
			$uri_parts = explode('/', trim($routedata['params'], ' /'));
			for($i=0; $i < count($uri_parts); $i+=2){
				$params[$uri_parts[$i]] = $uri_parts[$i+1];
			}
		}
	}
	return $params;
}
public function goPage(){

	if(isset($this->routedata)){
		$controller = new $this->routedata['class']($this);
		$controller->{$this->routedata['module']}();
	}else{
		$controller = new myBlogController($this);
		$controller->p404();
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