<?php

class Pagination{

private $count;
private $page;
private $links_perbar;
private $pages_count;
private $url;
private $class;
private $onclick;
private $prefix;

public $perpage;
public $start_pos;

function __construct($count,$page,$url,$perpage=25,$class='',$onclick='',$prefix=''){
$this->perpage=$perpage;
$this->links_perbar=4;
$this->count = $count;
$this->page = $page;
$this->url = $url;
$this->class = $class;
$this->onclick = 'onclick="'.$onclick.'(#page#);"';
$this->prefix = $prefix;
$this->pages_count = ceil($this->count / $this->perpage);
if($this->links_perbar > $this->pages_count)
	$this->links_perbar = $this->pages_count;
if($this->page > $this->pages_count) 
	$this->page = $this->pages_count;
$this->start_pos = ($this->page - 1) * $this->perpage;
if($this->start_pos < 0)
	$this->start_pos = 0;
if(($this->count - $this->start_pos) < $this->perpage)
  $this->perpage=$this->count - $this->start_pos;
}

public function getPaginationBar(){
if ($this->pages_count == 1) return false;
$separator = ' '; 

$style = 'style="color: #0000ff; text-decoration: none;"';
$style='';
$begin = $this->page - intval($this->links_perbar / 2);
$pagination_bar='';
unset($show_dots); 
if ($this->pages_count <= $this->links_perbar + 1) $show_dots = 'no';// Вывод ссылки на первую страницу
if (($begin > 2) && !isset($show_dots) && ($this->pages_count - $this->links_perbar > 2)) {
$pagination_bar.='<a class="'.$this->class.' first" '.$style.' '.str_replace('#page#','1',$this->onclick).' href="'.$this->url.$this->prefix.'page/1/'.'">|< </a> ';
}
for ($j = 0; $j < $this->page; $j++) {

if (($begin + $this->links_perbar - $j > $this->pages_count) && ($this->pages_count-$this->links_perbar + $j > 0)) {
$page_link = $this->pages_count - $this->links_perbar + $j; // Номер страницы

if (!isset($show_dots) && ($this->pages_count-$this->links_perbar > 1)) {
$pagination_bar.=' <a class="'.$this->class.'" '.$style.' '.str_replace('#page#',($page_link - 1),$this->onclick).' href="'.$this->url.$this->prefix.'page/'.($page_link - 1).'/"><b class="'.$this->class.'_elip">...</b></a> ';

$show_dots = "no";}
$pagination_bar.=' <a class="'.$this->class.'" '.$style.' '.str_replace('#page#',$page_link,$this->onclick).' href="'.$this->url.$this->prefix.'page/'.$page_link.'/">'.$page_link.'</a> '.$separator;
} else continue;
}
for ($j = 0; $j <= $this->links_perbar; $j++) // Основный цикл вывода ссылок
{
$i = $begin + $j; 

if ($i < 1) {
$this->links_perbar++;
continue;
}

if (!isset($show_dots) && $begin > 1) {
$pagination_bar.=' <a class="'.$this->class.'" '.$style.' '.str_replace('#page#',($i-1),$this->onclick).' href="'.$this->url.$this->prefix.'page/'.($i-1).'/"><b class="'.$this->class.'_elip">...</b></a> ';
$show_dots = "no";
}

if ($i > $this->pages_count) break;
if ($i == $this->page) {
$pagination_bar.=' <a class="'.$this->class.' cur" '.$style.' '.' ><b class="'.str_replace('#page#',$i,$this->class).'_cur">'.$i.'</b></a> ';
} else {
$pagination_bar.=' <a class="'.$this->class.'" '.$style.' '.str_replace('#page#',$i,$this->onclick).' href="'.$this->url.$this->prefix.'page/'.$i.'/">'.$i.'</a> ';
}

if (($i != $this->pages_count) && ($j != $this->links_perbar)) echo $separator;

if (($j == $this->links_perbar) && ($i < $this->pages_count)) {
$pagination_bar.=' <a class="'.$this->class.'" '.$style.' '.str_replace('#page#',($i+1),$this->onclick).' href="'.$this->url.$this->prefix.'page/'.($i+1).'/"><b class="'.$this->class.'_elip">...</b></a> ';
}
}

if ($begin + $this->links_perbar + 1 < $this->pages_count) {
$pagination_bar.=' <a class="'.$this->class.' last" '.$style.' '.str_replace('#page#',$this->pages_count,$this->onclick).' href="'.$this->url.$this->prefix.'page/'.$this->pages_count.'/">>| </a>';
}
return $pagination_bar;
}

}

?>